<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Models\EducationalResource;
use App\Models\EducationalResourceTag;
use App\Http\Controllers\Controller;
use App\Services\MediaLibraryService;
use Illuminate\Http\Request;
use App\Models\Media;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EducationRepositoryController extends Controller
{
    public function __construct(private readonly MediaLibraryService $mediaLibraryService)
    {
    }

    /**
     * Download the specified educational resource.
     */
    public function download($id)
    {
        $document = EducationalResource::findOrFail($id);

        if (!$document->isAccessibleBy(Auth::user())) {
            abort(403, __('You do not have permission to download this resource'));
        }

        if (!$document->file_path || !\Storage::disk('public')->exists($document->file_path)) {
            abort(404, __('Resource file not found'));
        }

        try {
            $document->incrementDownloadCount();

            \App\Models\EducationalResourceAccessLog::create([
                'educational_resource_id' => $document->id,
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'action' => 'download',
                'referrer' => request()->header('referer')
            ]);

            $filePath = \Storage::disk('public')->path($document->file_path);
            $fileName = $document->file_name ?: basename($filePath);

            return response()->download($filePath, $fileName);
        } catch (\Exception $e) {
            \Log::error('Education download failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'id' => $id,
            ]);
            return redirect()->back()->with('error', __('Failed to download resource'));
        }
    }

    /**
     * Change educational resource status (workflow action)
     */
    public function changeStatus(Request $request, $id)
    {
        $document = EducationalResource::findOrFail($id);
        $action = $request->input('action');
        $comment = $request->input('comment', '');

        // Derive available actions and target status
        $availableActions = method_exists($document, 'getAvailableActions') ? $document->getAvailableActions() : [];

        if (!isset($availableActions[$action])) {
            return response()->json([
                'success' => false,
                'message' => __('This action is not available for the current resource status or you do not have permission.')
            ], 403);
        }

        $targetStatus = $availableActions[$action]['target'];
        $oldStatus = $document->status;

        try {
            \DB::beginTransaction();

            $updateData = [
                'status' => $targetStatus,
                'updated_by' => Auth::id(),
            ];

            if ($targetStatus === EducationalResource::STATUS_PUBLISHED && !$document->published_at) {
                $updateData['published_at'] = now();
            }
            if ($targetStatus === EducationalResource::STATUS_APPROVED) {
                $updateData['approved_by'] = Auth::id();
            }

            $document->update($updateData);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Resource status updated successfully'),
                'data' => [
                    'new_status' => $targetStatus,
                    'status_display' => method_exists($document, 'getStatusDisplay') ? $document->getStatusDisplay() : $targetStatus,
                    'status_color' => method_exists($document, 'getStatusColor') ? $document->getStatusColor() : 'gray',
                    'available_actions' => method_exists($document, 'getAvailableActions') ? $document->getAvailableActions() : []
                ]
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Education status change failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Failed to update resource status: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Workflow history (placeholder)
     */
    public function workflowHistory($id)
    {
        // No dedicated workflow log model for education; return empty list
        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }

    /**
     * Increment download count (AJAX)
     */
    public function incrementDownload($id)
    {
        $document = EducationalResource::findOrFail($id);
        $document->incrementDownloadCount();
        return response()->json(['success' => true, 'download_count' => $document->download_count]);
    }

    /**
     * Preview resource (PDF inline if applicable)
     */
    public function preview($id)
    {
        $document = EducationalResource::findOrFail($id);
        if (!$document->isAccessibleBy(Auth::user())) {
            abort(403);
        }
        if (!$document->file_path || !\Storage::disk('public')->exists($document->file_path)) {
            abort(404);
        }
        // Fallback to download if not a PDF (no extension stored reliably)
        return redirect()->route('admin.education.download', $id);
    }

    /**
     * Basic download stats endpoint
     */
    public function downloadStats($id)
    {
        $document = EducationalResource::findOrFail($id);
        return response()->json([
            'success' => true,
            'download_count' => $document->download_count,
        ]);
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', EducationalResource::class);

        $breadcrumbs = [
            'title' => __('Educational Resource Hub'),
            'links' => [
                [
                    'name' => __('Dashboard'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Educational Resource Hub'),
                    'url' => '#',
                ],
            ],
        ];

        // Get documents with search, filter, and pagination
        // Map UI type labels to enum values stored in DB
        $typeMap = [
            'Curriculum Materials' => EducationalResource::TYPE_TEACHING_GUIDE,
            'Teaching Modules' => EducationalResource::TYPE_INTERACTIVE_MODULE,
            'Educational Videos' => EducationalResource::TYPE_VIDEO,
            'Podcasts' => EducationalResource::TYPE_PODCAST,
            'Interactive Learning Formats' => EducationalResource::TYPE_INTERACTIVE_MODULE,
            'Lesson Plans' => EducationalResource::TYPE_LESSON_PLAN,
        ];
        $allowedTypes = [
            EducationalResource::TYPE_VIDEO,
            EducationalResource::TYPE_PODCAST,
            EducationalResource::TYPE_INTERACTIVE_MODULE,
            EducationalResource::TYPE_LESSON_PLAN,
            EducationalResource::TYPE_TEACHING_GUIDE,
            EducationalResource::TYPE_PRESENTATION,
            EducationalResource::TYPE_CASE_STUDY,
            EducationalResource::TYPE_SIMULATION,
            EducationalResource::TYPE_OTHER,
        ];

        $query = EducationalResource::with(['creator', 'tags'])
            ->when($request->get('search'), function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('creator', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('resource_type', 'like', "%{$search}%")
                        ->orWhere('subject_area', 'like', "%{$search}%");
                });
            })
            ->when($request->get('type'), function ($query, $type) use ($typeMap, $allowedTypes) {
                $mapped = $typeMap[$type] ?? (in_array($type, $allowedTypes, true) ? $type : null);
                if ($mapped) {
                    return $query->where('resource_type', $mapped);
                }
                return $query;
            })
            ->when($request->get('category'), function ($query, $category) {
                return $query->where('subject_area', $category);
            })
            ->when($request->get('status'), function ($query, $status) {
                return $query->where('status', $status);
            });

        // Apply sorting
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $documents = $query->paginate(50);

        // Get statistics for the dashboard
        $stats = [
            'total' => EducationalResource::count(),
            'published' => EducationalResource::where('status', EducationalResource::STATUS_PUBLISHED)->count(),
            'draft' => EducationalResource::where('status', EducationalResource::STATUS_DRAFT)->count(),
            'under_review' => EducationalResource::where('status', EducationalResource::STATUS_UNDER_REVIEW)->count(),
            'featured' => EducationalResource::where('is_featured', true)->count(),
            'total_downloads' => EducationalResource::sum('download_count'),
        ];

        // Get upload limits for frontend
        $uploadLimits = [
            'max_file_uploads' => ini_get('max_file_uploads'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'effective_max_filesize' => min(
                $this->convertToBytes(ini_get('upload_max_filesize')),
                $this->convertToBytes(ini_get('post_max_size'))
            ),
            'effective_max_filesize_formatted' => $this->formatBytes(
                min(
                    $this->convertToBytes(ini_get('upload_max_filesize')),
                    $this->convertToBytes(ini_get('post_max_size'))
                )
            ),
            'post_max_size_formatted' => $this->formatBytes($this->convertToBytes(ini_get('post_max_size'))),
        ];

        return view('backend.pages.education.index', [
            'documents' => $documents,
            'breadcrumbs' => $breadcrumbs,
            'stats' => $stats,
            'uploadLimits' => $uploadLimits,
            'documentTypes' => [
                'Curriculum Materials',
                'Teaching Modules',
                'Educational Videos',
                'Podcasts',
                'Interactive Learning Formats',
                'Lesson Plans',
            ],
            'categories' => \App\Models\EducationalCategory::active()->ordered()->get(),
            'statuses' => [
                EducationalResource::STATUS_DRAFT,
                EducationalResource::STATUS_UNDER_REVIEW,
                EducationalResource::STATUS_APPROVED,
                EducationalResource::STATUS_PUBLISHED,
                EducationalResource::STATUS_ARCHIVED
            ]
        ]);
    }

    private function convertToBytes(string $size): int
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);

        if ($unit) {
            return (int) round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }

        return (int) round($size);
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Show the form for editing the specified document.
     */
    public function edit($id)
    {
        $document = EducationalResource::with('tags')->findOrFail($id);

        $this->authorize('update', $document);

        $breadcrumbs = [
            'title' => __('Edit Educational Resource Hub'),
            'links' => [
                [
                    'name' => __('Dashboard'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Educational Resource Hub'),
                    'url' => route('admin.education.index'),
                ],
                [
                    'name' => __('Edit Educational Resource Hub'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.education.edit', [
            'document' => $document,
            'breadcrumbs' => $breadcrumbs,
            'documentTypes' => [
                'Curriculum Materials',
                'Teaching Modules',
                'Educational Videos',
                'Podcasts',
                'Interactive Learning Formats',
                'Lesson Plans',
            ],
            'categories' => \App\Models\EducationalCategory::active()->ordered()->get(),
            'statuses' => [
                EducationalResource::STATUS_DRAFT => __('Draft'),
                EducationalResource::STATUS_UNDER_REVIEW => __('Under Review'),
                EducationalResource::STATUS_APPROVED => __('Approved'),
                EducationalResource::STATUS_PUBLISHED => __('Published'),
                EducationalResource::STATUS_ARCHIVED => __('Archived')
            ],
            'accessLevels' => [
                EducationalResource::ACCESS_PUBLIC => __('Public'),
                EducationalResource::ACCESS_PARTNER_ONLY => __('Partner Universities Only'),
                EducationalResource::ACCESS_INTERNAL_ONLY => __('Internal Only')
            ]
        ]);
    }

    /**
     * Update the specified document in storage.
     */
    public function update(Request $request, $id)
    {
        $document = EducationalResource::findOrFail($id);

        $this->authorize('update', $document);

        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_date' => 'required|date',
            'abstract' => 'required|string',
            'document_type' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'version' => 'nullable|string|max:50',
            'is_featured' => 'nullable|boolean',
            'access_level' => 'required|in:public,partner_only,internal_only',
            'status' => 'required|in:draft,under_review,approved,published,archived',
            'tags' => 'nullable|string|max:500',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,rtf,odt,ods,odp|max:102400', // 100MB max
        ]);

        try {
            \DB::beginTransaction();

            // Handle file upload if a new file is provided
            $fileData = [];
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();

                // Generate unique filename
                $filename = \Illuminate\Support\Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '.' . $extension;
                $filePath = $file->storeAs('documents', $filename, 'public');

                // Delete old file
                if ($document->file_path && \Storage::disk('public')->exists($document->file_path)) {
                    \Storage::disk('public')->delete($document->file_path);
                }

                $fileData = [
                    'file_path' => $filePath,
                    'file_name' => $originalName,
                    'file_size' => $fileSize,
                    'file_extension' => $extension,
                    'mime_type' => $mimeType,
                ];
            }

            // Map UI labels used in create to enum values stored in DB
            $typeMap = [
                'Curriculum Materials' => EducationalResource::TYPE_TEACHING_GUIDE,
                'Teaching Modules' => EducationalResource::TYPE_INTERACTIVE_MODULE,
                'Educational Videos' => EducationalResource::TYPE_VIDEO,
                'Podcasts' => EducationalResource::TYPE_PODCAST,
                'Interactive Learning Formats' => EducationalResource::TYPE_INTERACTIVE_MODULE,
                'Lesson Plans' => EducationalResource::TYPE_LESSON_PLAN,
            ];
            $allowedTypes = [
                EducationalResource::TYPE_VIDEO,
                EducationalResource::TYPE_PODCAST,
                EducationalResource::TYPE_INTERACTIVE_MODULE,
                EducationalResource::TYPE_LESSON_PLAN,
                EducationalResource::TYPE_TEACHING_GUIDE,
                EducationalResource::TYPE_PRESENTATION,
                EducationalResource::TYPE_CASE_STUDY,
                EducationalResource::TYPE_SIMULATION,
                EducationalResource::TYPE_OTHER,
            ];
            $resourceTypeInput = $validated['document_type'];
            $resourceType = $typeMap[$resourceTypeInput] ?? (in_array($resourceTypeInput, $allowedTypes, true) ? $resourceTypeInput : EducationalResource::TYPE_OTHER);

            // Update the document
            $document->update(array_merge([
                'title' => $validated['title'],
                // Map incoming fields to schema columns
                'creator' => $validated['author'],
                'description' => $validated['abstract'],
                'resource_type' => $resourceType,
                'subject_area' => $validated['category'],
                'published_at' => $validated['publication_date'] ? date('Y-m-d H:i:s', strtotime($validated['publication_date'])) : $document->published_at,
                'version' => $validated['version'] ?? $document->version,
                'is_featured' => $validated['is_featured'] ?? false,
                'access_level' => $validated['access_level'],
                'status' => $validated['status'],
                'updated_by' => Auth::id(),
            ], $fileData));

            // Handle tags
            if (!empty($validated['tags'])) {
                $this->processTags($document, $validated['tags']);
            } else {
                $document->tags()->detach();
            }

            // Handle status changes
            if ($validated['status'] === EducationalResource::STATUS_PUBLISHED && !$document->published_at) {
                $document->update(['published_at' => now()]);
            }

            \DB::commit();

            // Log the activity
            // activity()
            //     ->causedBy(Auth::user())
            //     ->performedOn($document)
            //     ->log('updated document: ' . $document->title);

            return redirect()->route('admin.education.index')
                ->with('success', __('Document updated successfully'));

        } catch (\Exception $e) {
            \DB::rollBack();

            \Log::error('Document update failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'document_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', __('Failed to update document: ') . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Publish a document
     */
    public function publish($id)
    {
        $document = EducationalResource::findOrFail($id);

        $this->authorize('publish', $document);

        try {
            $document->update([
                'status' => EducationalResource::STATUS_PUBLISHED,
                'published_at' => now(),
                'updated_by' => Auth::id(),
            ]);


            return response()->json([
                'success' => true,
                'message' => __('Document published successfully')
            ]);

        } catch (\Exception $e) {
            \Log::error('Document publish failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('Failed to publish document')
            ], 500);
        }
    }

    /**
     * Approve a document
     */
    public function approve($id)
    {
        $document = EducationalResource::findOrFail($id);

        $this->authorize('approve', $document);

        try {
            $document->update([
                'status' => EducationalResource::STATUS_APPROVED,
                'approved_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // activity()
            //     ->causedBy(Auth::user())
            //     ->performedOn($document)
            //     ->log('approved document: ' . $document->title);

            return response()->json([
                'success' => true,
                'message' => __('Document approved successfully')
            ]);

        } catch (\Exception $e) {
            \Log::error('Document approval failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('Failed to approve document')
            ], 500);
        }
    }

    public function store(Request $request)
    {

        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_date' => 'required|date',
            'abstract' => 'required|string',
            'document_type' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'version' => 'nullable|string|max:50',
            'is_featured' => 'nullable|boolean',
            'access_level' => 'required|in:public,partner_only,internal_only',
            'tags' => 'nullable|string|max:500',
            'files' => 'required|array',
            'files.*' => 'required|file|max:102400', // 100MB max
        ]);

        try {
            // Handle file upload
            $file = $request->file('files')[0]; // Get the first file since we're uploading one document
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Generate unique filename
            $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '.' . $extension;
            $filePath = $file->storeAs('education', $filename, 'public');

            // Map UI labels used in create to enum values stored in DB
            $typeMap = [
                'Curriculum Materials' => EducationalResource::TYPE_TEACHING_GUIDE,
                'Teaching Modules' => EducationalResource::TYPE_INTERACTIVE_MODULE,
                'Educational Videos' => EducationalResource::TYPE_VIDEO,
                'Podcasts' => EducationalResource::TYPE_PODCAST,
                'Interactive Learning Formats' => EducationalResource::TYPE_INTERACTIVE_MODULE,
                'Lesson Plans' => EducationalResource::TYPE_LESSON_PLAN,
            ];
            $allowedTypes = [
                EducationalResource::TYPE_VIDEO,
                EducationalResource::TYPE_PODCAST,
                EducationalResource::TYPE_INTERACTIVE_MODULE,
                EducationalResource::TYPE_LESSON_PLAN,
                EducationalResource::TYPE_TEACHING_GUIDE,
                EducationalResource::TYPE_PRESENTATION,
                EducationalResource::TYPE_CASE_STUDY,
                EducationalResource::TYPE_SIMULATION,
                EducationalResource::TYPE_OTHER,
            ];
            $resourceTypeInput = $validated['document_type'];
            $resourceType = $typeMap[$resourceTypeInput] ?? (in_array($resourceTypeInput, $allowedTypes, true) ? $resourceTypeInput : EducationalResource::TYPE_OTHER);

            // Create the document
            $document = EducationalResource::create([
                'title' => $validated['title'],
                // Map incoming fields to schema columns
                'creator' => $validated['author'],
                'description' => $validated['abstract'],
                'resource_type' => $resourceType,
                'subject_area' => $validated['category'],
                'file_path' => $filePath,
                'file_name' => $originalName,
                'file_size' => $fileSize,
                'file_extension' => $extension,
                'mime_type' => $mimeType,
                'version' => $validated['version'] ?? '1.0',
                'is_featured' => $validated['is_featured'] ?? false,
                'access_level' => $validated['access_level'],
                'status' => EducationalResource::STATUS_DRAFT, // Default to draft, can be changed via workflow
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Handle tags
            if (!empty($validated['tags'])) {
                $this->processTags($document, $validated['tags']);
            }


            return response()->json([
                'success' => true,
                'message' => __('File uploaded successfully'),
                'data' => $document
            ], 201);

        } catch (\Exception $e) {
            // Delete the file if it was uploaded but document creation failed
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            \Log::error('Document upload failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'file' => $request->file('files')[0]?->getClientOriginalName(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => __('Failed to upload file: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process and attach tags to the document
     */
    private function processTags(EducationalResource $document, string $tagsInput): void
    {
        $tags = array_map('trim', explode(',', $tagsInput));
        $tagIds = [];

        foreach ($tags as $tagName) {
            if (empty($tagName))
                continue;

            // Find or create tag
            $tag = EducationalResourceTag::firstOrCreate(
                ['name' => $tagName],
                [
                    'slug' => Str::slug($tagName),
                    'color' => $this->generateTagColor($tagName)
                ]
            );

            $tagIds[] = $tag->id;
        }

        // Sync tags with the document
        $document->tags()->sync($tagIds);
    }

    /**
     * Generate a consistent color for a tag based on its name
     */
    private function generateTagColor(string $tagName): string
    {
        $colors = [
            '#3b82f6',
            '#ef4444',
            '#10b981',
            '#f59e0b',
            '#8b5cf6',
            '#ec4899',
            '#06b6d4',
            '#84cc16',
            '#f97316',
            '#6b7280'
        ];

        $hash = crc32($tagName);
        return $colors[$hash % count($colors)];
    }

    /**
     * Get upload limits for the frontend
     */
    public function getUploadLimits()
    {
        $maxFileSize = ini_get('upload_max_filesize');
        $postMaxSize = ini_get('post_max_size');
        $maxFileUploads = ini_get('max_file_uploads');

        // Convert to bytes for calculations
        $maxFileSizeBytes = $this->convertToBytes($maxFileSize);
        $postMaxSizeBytes = $this->convertToBytes($postMaxSize);

        // Use the smaller of upload_max_filesize and post_max_size as effective limit
        $effectiveMaxFilesize = min($maxFileSizeBytes, $postMaxSizeBytes);

        return response()->json([
            'max_file_uploads' => (int) $maxFileUploads,
            'upload_max_filesize' => $maxFileSize,
            'post_max_size' => $postMaxSize,
            'effective_max_filesize' => $effectiveMaxFilesize,
            'effective_max_filesize_formatted' => $this->formatBytes($effectiveMaxFilesize),
            'post_max_size_formatted' => $this->formatBytes($postMaxSizeBytes),
        ]);
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy($id)
    {
        $document = EducationalResource::findOrFail($id);

        $this->authorize('delete', $document);

        try {
            \DB::beginTransaction();

            // Store document info for logging before deletion
            $documentTitle = $document->title;
            $filePath = $document->file_path;

            // Delete associated tags
            $document->tags()->detach();

            // Delete access logs
            $document->accessLogs()->delete();

            // Delete the document
            $document->delete();

            // Delete the physical file
            if ($filePath && \Storage::disk('public')->exists($filePath)) {
                \Storage::disk('public')->delete($filePath);
            }

            \DB::commit();

            // Log the activity
            // activity()
            //     ->causedBy(Auth::user())
            //     ->log('deleted document: ' . $documentTitle);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Document deleted successfully')
                ]);
            }

            return redirect()->route('admin.document.index')
                ->with('success', __('Document deleted successfully'));

        } catch (\Exception $e) {
            \DB::rollBack();

            \Log::error('Document deletion failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'document_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to delete document: ') . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('Failed to delete document: ') . $e->getMessage());
        }
    }

    /**
     * Bulk delete documents
     */
    public function bulkDelete(Request $request)
    {
        $documentIds = $request->input('ids', []);

        if (empty($documentIds)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('No documents selected for deletion')
                ], 400);
            }
            return redirect()->back()->with('error', __('No documents selected for deletion'));
        }

        try {
            \DB::beginTransaction();

            $deletedCount = 0;
            $failedCount = 0;
            $deletedTitles = [];

            foreach ($documentIds as $documentId) {
                $document = EducationalResource::find($documentId);

                if (!$document) {
                    $failedCount++;
                    continue;
                }

                // Check authorization for each document
                if (!Auth::user()->can('delete', $document)) {
                    $failedCount++;
                    continue;
                }

                $documentTitle = $document->title;
                $filePath = $document->file_path;

                // Delete associated data
                $document->tags()->detach();
                $document->accessLogs()->delete();

                // Delete the document
                if ($document->delete()) {
                    // Delete the physical file
                    if ($filePath && \Storage::disk('public')->exists($filePath)) {
                        \Storage::disk('public')->delete($filePath);
                    }

                    $deletedCount++;
                    $deletedTitles[] = $documentTitle;
                } else {
                    $failedCount++;
                }
            }

            \DB::commit();

            // Log the activity
            // if ($deletedCount > 0) {
            //     activity()
            //         ->causedBy(Auth::user())
            //         ->log('bulk deleted ' . $deletedCount . ' documents: ' . implode(', ', $deletedTitles));
            // }

            $message = '';
            if ($deletedCount > 0) {
                $message .= __(':count document(s) deleted successfully.', ['count' => $deletedCount]);
            }
            if ($failedCount > 0) {
                $message .= ' ' . __(':count document(s) failed to delete.', ['count' => $failedCount]);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => $deletedCount > 0,
                    'message' => trim($message),
                    'deleted_count' => $deletedCount,
                    'failed_count' => $failedCount
                ]);
            }

            return redirect()->route('admin.education.index')
                ->with($deletedCount > 0 ? 'success' : 'error', trim($message));

        } catch (\Exception $e) {
            \DB::rollBack();

            \Log::error('Bulk document deletion failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'document_ids' => $documentIds,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to delete documents: ') . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('Failed to delete documents: ') . $e->getMessage());
        }
    }

    /**
     * Force delete a document (for super admins)
     */
    public function forceDelete($id)
    {
        $document = EducationalResource::withTrashed()->findOrFail($id);

        $this->authorize('forceDelete', $document);

        try {
            \DB::beginTransaction();

            $documentTitle = $document->title;
            $filePath = $document->file_path;

            // Permanently delete associated data
            $document->tags()->detach();
            $document->accessLogs()->forceDelete();

            // Force delete the document
            $document->forceDelete();

            // Delete the physical file
            if ($filePath && \Storage::disk('public')->exists($filePath)) {
                \Storage::disk('public')->delete($filePath);
            }

            \DB::commit();

            // activity()
            //     ->causedBy(Auth::user())
            //     ->log('permanently deleted document: ' . $documentTitle);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Document permanently deleted')
                ]);
            }

            return redirect()->route('admin.education.index')
                ->with('success', __('Document permanently deleted'));

        } catch (\Exception $e) {
            \DB::rollBack();

            \Log::error('Force document deletion failed: ' . $e->getMessage());

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to permanently delete document')
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('Failed to permanently delete document'));
        }
    }

    /**
     * Restore a soft-deleted document
     */
    public function restore($id)
    {
        $document = EducationalResource::withTrashed()->findOrFail($id);

        $this->authorize('restore', $document);

        try {
            $document->restore();

            // activity()
            //     ->causedBy(Auth::user())
            //     ->performedOn($document)
            //     ->log('restored document: ' . $document->title);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Document restored successfully')
                ]);
            }

            return redirect()->route('admin.document.index')
                ->with('success', __('Document restored successfully'));

        } catch (\Exception $e) {
            \Log::error('Document restoration failed: ' . $e->getMessage());

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to restore document')
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('Failed to restore document'));
        }
    }


    public function api(Request $request)
    {
        $this->authorize('viewAny', EducationalResource::class);

        $result = $this->mediaLibraryService->getMediaList(
            $request->get('search'),
            $request->get('type'),
            $request->get('sort', 'created_at'),
            $request->get('direction', 'desc'),
            (int) $request->get('per_page', 100)
        );

        // Transform media for API response.
        $mediaItems = $result['media']->map(function ($item) {
            $url = '';
            $thumbnailUrl = '';

            try {
                if (empty($item->model_type) || $item->model_id == 0) {
                    $url = asset('storage/media/' . $item->file_name);
                    $thumbnailUrl = $url;
                } else {
                    $url = $item->getUrl();
                    $thumbnailUrl = $item->hasGeneratedConversion('thumb') ? $item->getUrl('thumb') : $item->getUrl();
                }
            } catch (\Exception $e) {
                $url = asset('storage/media/' . $item->file_name);
                $thumbnailUrl = $url;
            }

            return [
                'id' => $item->id,
                'name' => $item->name,
                'file_name' => $item->file_name,
                'mime_type' => $item->mime_type,
                'size' => $item->size,
                'human_readable_size' => $item->human_readable_size,
                'url' => $url,
                'thumbnail_url' => $thumbnailUrl,
                'extension' => pathinfo($item->file_name, PATHINFO_EXTENSION),
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                'collection_name' => $item->collection_name ?? 'default',
                'model_type' => $item->model_type,
                'model_id' => $item->model_id,
                'is_standalone' => empty($item->model_type) || $item->model_id == 0,
            ];
        });

        return response()->json([
            'success' => true,
            'media' => $mediaItems,
            'stats' => $result['stats'],
            'pagination' => [
                'current_page' => $result['media']->currentPage(),
                'last_page' => $result['media']->lastPage(),
                'per_page' => $result['media']->perPage(),
                'total' => $result['media']->total(),
                'has_more_pages' => $result['media']->hasMorePages(),
            ],
        ]);
    }


}