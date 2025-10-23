<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\MediaLibraryService;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentTag;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DocumentRepositoryController extends Controller
{
    public function __construct(private readonly MediaLibraryService $mediaLibraryService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Document::class);

        $breadcrumbs = [
            'title' => __('Document Repository'),
            'links' => [
                [
                    'name' => __('Dashboard'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Document Repository'),
                    'url' => '#',
                ],
            ],
        ];

        // Get documents with search, filter, and pagination
        $query = Document::with(['creator', 'tags'])
            ->when($request->get('search'), function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->orWhere('abstract', 'like', "%{$search}%")
                        ->orWhere('document_type', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->when($request->get('type'), function ($query, $type) {
                return $query->where('document_type', $type);
            })
            ->when($request->get('category'), function ($query, $category) {
                return $query->where('category', $category);
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
            'total' => Document::count(),
            'published' => Document::where('status', Document::STATUS_PUBLISHED)->count(),
            'draft' => Document::where('status', Document::STATUS_DRAFT)->count(),
            'under_review' => Document::where('status', Document::STATUS_UNDER_REVIEW)->count(),
            'featured' => Document::where('is_featured', true)->count(),
            'total_downloads' => Document::sum('download_count'),
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

        return view('backend.pages.document.index', [
            'documents' => $documents,
            'breadcrumbs' => $breadcrumbs,
            'stats' => $stats,
            'uploadLimits' => $uploadLimits,
            'documentTypes' => [
                'Policy Brief',
                'Research Paper',
                'Annual Report',
                'Quarterly Report',
                'Assessment Report',
                'AHC Guideline',
                'Educational Material',
                'Newsletter',
                'Other'
            ],
            'categories' => \App\Models\DocumentCategory::active()->ordered()->get(),
            'statuses' => [
                Document::STATUS_DRAFT,
                Document::STATUS_UNDER_REVIEW,
                Document::STATUS_APPROVED,
                Document::STATUS_PUBLISHED,
                Document::STATUS_ARCHIVED
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
        $document = Document::with('tags')->findOrFail($id);

        $this->authorize('update', $document);

        $breadcrumbs = [
            'title' => __('Edit Document'),
            'links' => [
                [
                    'name' => __('Dashboard'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Document Repository'),
                    'url' => route('admin.document.index'),
                ],
                [
                    'name' => __('Edit Document'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.document.edit', [
            'document' => $document,
            'breadcrumbs' => $breadcrumbs,
            'documentTypes' => [
                'Policy Brief',
                'Research Paper',
                'Annual Report',
                'Quarterly Report',
                'Assessment Report',
                'AHC Guideline',
                'Educational Material',
                'Newsletter',
                'Other'
            ],
            'categories' => \App\Models\DocumentCategory::active()->ordered()->get(),
            'statuses' => [
                Document::STATUS_DRAFT => __('Draft'),
                Document::STATUS_UNDER_REVIEW => __('Under Review'),
                Document::STATUS_APPROVED => __('Approved'),
                Document::STATUS_PUBLISHED => __('Published'),
                Document::STATUS_ARCHIVED => __('Archived')
            ],
            'accessLevels' => [
                Document::ACCESS_PUBLIC => __('Public'),
                Document::ACCESS_PARTNER_ONLY => __('Partner Universities Only'),
                Document::ACCESS_INTERNAL_ONLY => __('Internal Only')
            ]
        ]);
    }

    /**
     * Update the specified document in storage.
     */
    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        $this->authorize('update', $document);

        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_date' => 'required|date',
            'abstract' => 'required|string',
            'document_type' => 'required|string|in:Policy Brief,Research Paper,Annual Report,Quarterly Report,Assessment Report,AHC Guideline,Educational Material,Newsletter,Other',
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

            // Update the document
            $document->update(array_merge([
                'title' => $validated['title'],
                'author' => $validated['author'],
                'publication_date' => $validated['publication_date'],
                'abstract' => $validated['abstract'],
                'document_type' => $validated['document_type'],
                'category' => $validated['category'],
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
            if ($validated['status'] === Document::STATUS_PUBLISHED && !$document->published_at) {
                $document->update(['published_at' => now()]);
            }

            \DB::commit();

            // Log the activity
            // activity()
            //     ->causedBy(Auth::user())
            //     ->performedOn($document)
            //     ->log('updated document: ' . $document->title);

            return redirect()->route('admin.document.index')
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
        $document = Document::findOrFail($id);

        $this->authorize('publish', $document);

        try {
            $document->update([
                'status' => Document::STATUS_PUBLISHED,
                'published_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            // activity()
            //     ->causedBy(Auth::user())
            //     ->performedOn($document)
            //     ->log('published document: ' . $document->title);

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
        $document = Document::findOrFail($id);

        $this->authorize('approve', $document);

        try {
            $document->update([
                'status' => Document::STATUS_APPROVED,
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
            'document_type' => 'required|string|in:Policy Brief,Research Paper,Annual Report,Quarterly Report,Assessment Report,AHC Guideline,Educational Material,Newsletter,Other',
            'category' => 'required|string|max:255',
            'version' => 'nullable|string|max:50',
            'is_featured' => 'nullable|boolean',
            'access_level' => 'required|in:public,partner_only,internal_only',
            'tags' => 'nullable|string|max:500',
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,rtf,odt,ods,odp|max:102400', // 100MB max
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
            $filePath = $file->storeAs('documents', $filename, 'public');

            // Create the document
            $document = Document::create([
                'title' => $validated['title'],
                'author' => $validated['author'],
                'publication_date' => $validated['publication_date'],
                'abstract' => $validated['abstract'],
                'document_type' => $validated['document_type'],
                'category' => $validated['category'],
                'file_path' => $filePath,
                'file_name' => $originalName,
                'file_size' => $fileSize,
                'file_extension' => $extension,
                'mime_type' => $mimeType,
                'version' => $validated['version'] ?? '1.0',
                'is_featured' => $validated['is_featured'] ?? false,
                'access_level' => $validated['access_level'],
                'status' => Document::STATUS_DRAFT, // Default to draft, can be changed via workflow
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Handle tags
            if (!empty($validated['tags'])) {
                $this->processTags($document, $validated['tags']);
            }


            return response()->json([
                'success' => true,
                'message' => __('Document uploaded successfully'),
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
                'message' => __('Failed to upload document: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process and attach tags to the document
     */
    private function processTags(Document $document, string $tagsInput): void
    {
        $tags = array_map('trim', explode(',', $tagsInput));
        $tagIds = [];

        foreach ($tags as $tagName) {
            if (empty($tagName))
                continue;

            // Find or create tag
            $tag = DocumentTag::firstOrCreate(
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
     * Convert size string to bytes
     */
    // private function convertToBytes(string $size): int
    // {
    //     $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
    //     $size = preg_replace('/[^0-9\.]/', '', $size);

    //     if ($unit) {
    //         return (int) round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    //     }

    //     return (int) round($size);
    // }

    /**
     * Format bytes to human readable format
     */
    // private function formatBytes(int $bytes, int $precision = 2): string
    // {
    //     $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    //     for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
    //         $bytes /= 1024;
    //     }

    //     return round($bytes, $precision) . ' ' . $units[$i];
    // }

    /**
     * Remove the specified document from storage.
     */
    public function destroy($id)
    {
        $document = Document::findOrFail($id);

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
                $document = Document::find($documentId);

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

            return redirect()->route('admin.document.index')
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
        $document = Document::withTrashed()->findOrFail($id);

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

            return redirect()->route('admin.document.index')
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
        $document = Document::withTrashed()->findOrFail($id);

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
        $this->authorize('viewAny', Media::class);

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

    /**
     * Get upload limits for frontend consumption
     */
    // public function getUploadLimits()
    // {
    //     $this->authorize('viewAny', Media::class);

    //     $limits = MediaHelper::getUploadLimits();

    //     // Add demo mode restrictions info
    //     if (config('app.demo_mode', false)) {
    //         $limits['demo_mode'] = true;
    //         $limits['allowed_mime_types'] = MediaHelper::getAllowedMimeTypesForDemo();
    //         $limits['demo_restriction_message'] = __('In demo mode, only images, videos, PDFs, and documents (Word, Excel, PowerPoint, text files) are allowed.');
    //     } else {
    //         $limits['demo_mode'] = false;
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'limits' => $limits,
    //     ]);
    // }

    /**
     * Download the specified document.
     */
    public function download($id)
    {
        $document = Document::findOrFail($id);

        // Check if user has permission to download based on access level
        if (!$document->isAccessibleBy(Auth::user())) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('You do not have permission to download this document')
                ], 403);
            }
            abort(403, __('You do not have permission to download this document'));
        }

        // Check if file exists
        if (!\Storage::disk('public')->exists($document->file_path)) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Document file not found')
                ], 404);
            }
            abort(404, __('Document file not found'));
        }

        try {
            // Increment download count
            $document->incrementDownloadCount();

            // Log the download activity
            // activity()
            //     ->causedBy(Auth::user())
            //     ->performedOn($document)
            //     ->withProperties([
            //         'ip_address' => request()->ip(),
            //         'user_agent' => request()->userAgent(),
            //         'download_count' => $document->download_count + 1
            //     ])
            //     ->log('downloaded document: ' . $document->title);

            // Create download log entry
            \App\Models\DocumentAccessLog::create([
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'action' => 'download',
                'referrer' => request()->header('referer')
            ]);

            // Get file path and set appropriate headers
            $filePath = \Storage::disk('public')->path($document->file_path);
            $fileName = $document->file_name;

            // Set appropriate headers for download
            $headers = [
                'Content-Type' => $document->mime_type,
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Content-Length' => $document->file_size,
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];

            return response()->download($filePath, $fileName, $headers);

        } catch (\Exception $e) {
            \Log::error('Document download failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'document_id' => $id,
                'file_path' => $document->file_path ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to download document: ') . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('Failed to download document: ') . $e->getMessage());
        }
    }

    /**
     * Preview document (inline viewing)
     */
    public function preview($id)
    {
        $document = Document::findOrFail($id);

        // Check if user has permission to view
        if (!$document->isAccessibleBy(Auth::user())) {
            abort(403, __('You do not have permission to view this document'));
        }

        // Only allow preview for PDF files
        if ($document->file_extension !== 'pdf') {
            return redirect()->route('admin.document.download', $id);
        }

        // Check if file exists
        if (!\Storage::disk('public')->exists($document->file_path)) {
            abort(404, __('Document file not found'));
        }

        try {
            // Log the preview activity
            // activity()
            //     ->causedBy(Auth::user())
            //     ->performedOn($document)
            //     ->withProperties([
            //         'ip_address' => request()->ip(),
            //         'user_agent' => request()->userAgent()
            //     ])
            //     ->log('previewed document: ' . $document->title);

            // Create preview log entry
            \App\Models\DocumentAccessLog::create([
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'action' => 'preview',
                'referrer' => request()->header('referer')
            ]);

            // Increment view count (but not download count)
            $document->incrementViewCount();

            // Get file path and set appropriate headers for inline viewing
            $filePath = \Storage::disk('public')->path($document->file_path);

            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $document->file_name . '"',
                'Content-Length' => $document->file_size,
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];

            return response()->file($filePath, $headers);

        } catch (\Exception $e) {
            \Log::error('Document preview failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', __('Failed to preview document: ') . $e->getMessage());
        }
    }

    /**
     * Get document download statistics
     */
    public function downloadStats($id)
    {
        $document = Document::findOrFail($id);

        $this->authorize('view', $document);

        $stats = [
            'total_downloads' => $document->download_count,
            'total_views' => $document->view_count,
            'recent_downloads' => $document->accessLogs()
                ->where('action', 'download')
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
            'download_trend' => $this->getDownloadTrend($document),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get download trend data for charts
     */
    private function getDownloadTrend(Document $document)
    {
        $trend = $document->accessLogs()
            ->where('action', 'download')
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $trend;
    }

    /**
     * Increment download count via AJAX
     */
    public function incrementDownload($id)
    {
        $document = Document::findOrFail($id);

        if (!$document->isAccessibleBy(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => __('Permission denied')
            ], 403);
        }

        try {
            $document->incrementDownloadCount();

            // Log the download activity
            \App\Models\DocumentAccessLog::create([
                'document_id' => $document->id,
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'action' => 'download',
                'referrer' => request()->header('referer')
            ]);

            return response()->json([
                'success' => true,
                'download_count' => $document->download_count
            ]);

        } catch (\Exception $e) {
            \Log::error('Download count increment failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('Failed to track download')
            ], 500);
        }
    }

    /**
     * Increment view count via AJAX
     */
    public function incrementView($id)
    {
        $document = Document::findOrFail($id);

        if (!$document->isAccessibleBy(Auth::user())) {
            return response()->json(['success' => false], 403);
        }

        $document->incrementViewCount();

        \App\Models\DocumentAccessLog::create([
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'action' => 'view',
            'referrer' => request()->header('referer')
        ]);

        return response()->json(['success' => true]);
    }
}