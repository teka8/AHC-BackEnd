<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Models\Media;
use App\Models\Others;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\MediaLibraryService;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Notifications\OthersStatusChanged;
use App\Events\NewContentPublished;

class OthersController extends Controller
{
    public function __construct(private readonly MediaLibraryService $mediaLibraryService)
    {
    }

    /**
     * Download the specified Others resource.
     */
    public function download($id)
    {
        $document = Others::findOrFail($id);

        if (! $document->isAccessibleBy(Auth::user())) {
            abort(403, __('You do not have permission to download this file'));
        }

        if (! $document->file_path || ! \Storage::disk('public')->exists($document->file_path)) {
            abort(404, __('File not found'));
        }

        try {
            $document->incrementDownloadCount();

            \App\Models\OthersAccessLog::create([
                'others_id' => $document->id,
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'action' => 'download',
                'referrer' => request()->header('referer'),
            ]);

            $filePath = \Storage::disk('public')->path($document->file_path);
            $fileName = $document->file_name ?: basename($filePath);
            return response()->download($filePath, $fileName);
        } catch (\Exception $e) {
            \Log::error('Others download failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'id' => $id,
            ]);
            return redirect()->back()->with('error', __('Failed to download file'));
        }
    }

    /**
     * Increment download count (AJAX)
     */
    public function incrementDownload($id)
    {
        $document = Others::findOrFail($id);
        $document->incrementDownloadCount();
        return response()->json(['success' => true, 'download_count' => $document->download_count]);
    }

    /**
     * Preview resource (fallback to download)
     */
    public function preview($id)
    {
        $document = Others::findOrFail($id);
        if (! $document->isAccessibleBy(Auth::user())) {
            abort(403);
        }
        if (! $document->file_path || ! \Storage::disk('public')->exists($document->file_path)) {
            abort(404);
        }
        return redirect()->route('admin.others.download', $id);
    }

    /**
     * Basic download stats
     */
    public function downloadStats($id)
    {
        $document = Others::findOrFail($id);
        return response()->json([
            'success' => true,
            'download_count' => $document->download_count,
        ]);
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Others::class);

        $breadcrumbs = [
            'title' => __('Others'),
            'links' => [
                [
                    'name' => __('Dashboard'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Others'),
                    'url' => '#',
                ],
            ],
        ];

        // Get documents with search, filter, and pagination
        $query = Others::with(['creator'])
            ->when($request->get('search'), function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('creator', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('resource_type', 'like', "%{$search}%")
                        ->orWhere('subject_area', 'like', "%{$search}%");
                });
            })
            ->when($request->get('type'), function ($query, $type) {
                return $query->where('resource_type', $type);
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
            'total' => Others::count(),
            'published' => Others::where('status', Others::STATUS_PUBLISHED)->count(),
            'draft' => Others::where('status', Others::STATUS_DRAFT)->count(),
            'under_review' => Others::where('status', Others::STATUS_UNDER_REVIEW)->count(),
            'featured' => Others::where('is_featured', true)->count(),
            'total_downloads' => Others::sum('download_count'),
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

        return view('backend.pages.others.index', [
            'documents' => $documents,
            'breadcrumbs' => $breadcrumbs,
            'stats' => $stats,
            'uploadLimits' => $uploadLimits,
            'documentTypes' => [
                'Newsletter',
                'Presentation',
            ],
            'categories' => \App\Models\OthersCategory::active()->ordered()->get(),
            'statuses' => [
                Others::STATUS_DRAFT,
                Others::STATUS_UNDER_REVIEW,
                Others::STATUS_APPROVED,
                Others::STATUS_PUBLISHED,
                Others::STATUS_ARCHIVED,
            ],
        ]);
    }

    private function convertToBytes(string $size): int
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $sizeValue = preg_replace('/[^0-9\.]/', '', $size);

        if ($unit) {
            return (int) round((float)$sizeValue * pow(1024, stripos('bkmgtpezy', $unit[0] ?? 'b')));
        }

        return (int) round((float)$sizeValue);
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
        $document = Others::with(['newsletterArticles'])->findOrFail($id);
        $this->authorize('update', $document);

        $breadcrumbs = [
            'title' => __('Edit Other Resource'),
            'links' => [
                [
                    'name' => __('Dashboard'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Others'),
                    'url' => route('admin.others.index'),
                ],
                [
                    'name' => __('Edit Other Resource'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.others.edit', [
            'document' => $document,
            'breadcrumbs' => $breadcrumbs,
            'documentTypes' => [
                'Newsletter',
                'Presentation',
            ],
            'categories' => \App\Models\OthersCategory::active()->ordered()->get(),
            'statuses' => [
                Others::STATUS_DRAFT => __('Draft'),
                Others::STATUS_UNDER_REVIEW => __('Under Review'),
                Others::STATUS_APPROVED => __('Approved'),
                Others::STATUS_PUBLISHED => __('Published'),
                Others::STATUS_ARCHIVED => __('Archived'),
            ],
            'accessLevels' => [
                Others::ACCESS_PUBLIC => __('Public'),
                Others::ACCESS_PARTNER_ONLY => __('Partner Universities Only'),
                Others::ACCESS_INTERNAL_ONLY => __('Internal Only'),
            ],
        ]);
    }

    /**
     * Update the specified document in storage.
     */
    public function update(Request $request, $id)
    {
        $document = Others::findOrFail($id);
        $this->authorize('update', $document);

        $isNewsletter = $request->input('document_type') === 'Newsletter';

        // Validate the request
        $rules = [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_date' => 'required|date',
            'abstract' => $isNewsletter ? 'nullable|string' : 'required|string',
            'document_type' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'is_featured' => 'nullable|boolean',
            'access_level' => 'required|in:public,partner_only,internal_only',
            'status' => 'required|in:draft,under_review,approved,published,archived',
            'tags' => 'nullable|string|max:500',
            'newsletter_volume' => $isNewsletter ? 'nullable|string|max:50' : 'nullable',
            'newsletter_issue' => $isNewsletter ? 'nullable|string|max:50' : 'nullable',
        ];

        if ($isNewsletter) {
            $rules['articles'] = 'required|array|min:1';
            $rules['articles.*.title'] = 'required|string|max:255';
            $rules['articles.*.content'] = 'required|string';
            $rules['articles.*.image'] = 'nullable|image|max:5120';
        } else {
            $rules['file'] = 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,rtf,odt,ods,odp|max:102400';
        }

        $validated = $request->validate($rules);

        try {
            \DB::beginTransaction();

            $fileData = [];
            if (!$isNewsletter && $request->hasFile('file')) {
                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
                $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('others', $filename, 'public');

                if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                    Storage::disk('public')->delete($document->file_path);
                }

                $fileData = [
                    'file_path' => $filePath,
                    'file_name' => $originalName,
                    'file_size' => $file->getSize(),
                ];
            }

            // Map UI labels
            $typeMap = [
                'Newsletter' => Others::TYPE_NEWSLETTER,
                'Presentation' => Others::TYPE_PRESENTATION,
            ];
            $resourceType = $typeMap[$validated['document_type']] ?? Others::TYPE_PRESENTATION;

            $oldStatus = $document->status;

            // Update the document
            $document->update(array_merge([
                'title' => $validated['title'],
                'creator' => $validated['author'],
                'description' => $validated['abstract'] ?? '',
                'resource_type' => $resourceType,
                'subject_area' => trim($validated['category']),
                'published_at' => $validated['publication_date'] ? date('Y-m-d H:i:s', strtotime($validated['publication_date'])) : $document->published_at,
                'is_featured' => $validated['is_featured'] ?? false,
                'access_level' => $validated['access_level'],
                'status' => $validated['status'],
                'newsletter_volume' => $validated['newsletter_volume'] ?? null,
                'newsletter_issue' => $validated['newsletter_issue'] ?? null,
                'updated_by' => Auth::id(),
            ], $fileData));

            // Trigger notification if status changed to published and it's a newsletter
            if ($oldStatus !== Others::STATUS_PUBLISHED && $document->status === Others::STATUS_PUBLISHED && $document->resource_type === Others::TYPE_NEWSLETTER) {
                event(new NewContentPublished($document, 'newsletters'));
            }

            // Handle tags
            if (!empty($validated['tags'])) {
                $this->processTags($document, $validated['tags']);
            } else {
                $document->tags = [];
                $document->save();
            }

            // Handle Newspaper Articles sync
            if ($isNewsletter && $request->has('articles')) {
                $keepArticleIds = [];
                foreach ($request->input('articles') as $index => $articleData) {
                    $articleId = $articleData['id'] ?? null;
                    
                    $articleImagePath = null;
                    if ($request->hasFile("articles.{$index}.image")) {
                        $imageFile = $request->file("articles.{$index}.image");
                        $imageName = 'newsletter_' . time() . '_' . $index . '.' . $imageFile->getClientOriginalExtension();
                        $articleImagePath = $imageFile->storeAs('newsletter_images', $imageName, 'public');
                    }

                    $articlePayload = [
                        'title' => $articleData['title'],
                        'content' => $articleData['content'],
                        'sort_order' => $index,
                    ];

                    if ($articleImagePath) {
                        $articlePayload['image_path'] = $articleImagePath;
                    }

                    if ($articleId) {
                        $article = $document->newsletterArticles()->find($articleId);
                        if ($article) {
                            $article->update($articlePayload);
                            $keepArticleIds[] = $article->id;
                        } else {
                            $newArticle = $document->newsletterArticles()->create($articlePayload);
                            $keepArticleIds[] = $newArticle->id;
                        }
                    } else {
                        $newArticle = $document->newsletterArticles()->create($articlePayload);
                        $keepArticleIds[] = $newArticle->id;
                    }
                }
                // Delete articles not in the request
                $document->newsletterArticles()->whereNotIn('id', $keepArticleIds)->delete();
            }

            \DB::commit();

            return redirect()->route('admin.others.index')
                ->with('success', __('Document updated successfully'));

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Document update failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'document_id' => $id,
                'trace' => $e->getTraceAsString(),
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
        $document = Others::findOrFail($id);

        $this->authorize('publish', $document);

        try {
            $document->update([
                'status' => Others::STATUS_PUBLISHED,
                'published_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('Document published successfully'),
            ]);

        } catch (\Exception $e) {
            \Log::error('Document publish failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('Failed to publish document'),
            ], 500);
        }
    }

    /**
     * Approve a document
     */
    public function approve($id)
    {
        $document = Others::findOrFail($id);

        $this->authorize('approve', $document);

        try {
            $document->update([
                'status' => Others::STATUS_APPROVED,
                'approved_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // activity()
            //     ->causedBy(Auth::user())
            //     ->performedOn($document)
            //     ->log('approved document: ' . $document->title);

            return response()->json([
                'success' => true,
                'message' => __('Document approved successfully'),
            ]);

        } catch (\Exception $e) {
            \Log::error('Document approval failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('Failed to approve document'),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        // Determine if it's a newsletter to apply different validation
        $isNewsletter = $request->input('document_type') === 'Newsletter';

        // Validate the request
        $rules = [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_date' => 'required|date',
            'abstract' => $isNewsletter ? 'nullable|string' : 'required|string',
            'document_type' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'is_featured' => 'nullable|boolean',
            'status' => 'nullable|in:draft,under_review,approved,published,archived',
            'tags' => 'nullable|string|max:500',
            'newsletter_volume' => $isNewsletter ? 'nullable|string|max:50' : 'nullable',
            'newsletter_issue' => $isNewsletter ? 'nullable|string|max:50' : 'nullable',
        ];

        if ($isNewsletter) {
            $rules['articles'] = 'required|array|min:1';
            $rules['articles.*.title'] = 'required|string|max:255';
            $rules['articles.*.content'] = 'required|string';
            $rules['articles.*.image'] = 'nullable|image|max:5120'; // 5MB max per image
        } else {
            $rules['files'] = 'required|array';
            $rules['files.*'] = 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,rtf,odt,ods,odp|max:102400';
        }

        $validated = $request->validate($rules);

        try {
            \DB::beginTransaction();

            // Map UI labels
            $typeMap = [
                'Newsletter' => Others::TYPE_NEWSLETTER,
                'Presentation' => Others::TYPE_PRESENTATION,
            ];
            $resourceType = $typeMap[$validated['document_type']] ?? Others::TYPE_PRESENTATION;

            // Handle standard file upload for non-newsletters
            $filePath = null;
            $originalName = null;
            $fileSize = null;

            if (!$isNewsletter) {
                $file = $request->file('files')[0];
                $originalName = $file->getClientOriginalName();
                $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('others', $filename, 'public');
                $fileSize = $file->getSize();
            }

            // Create the main document entry
            $document = Others::create([
                'title' => $validated['title'],
                'creator' => $validated['author'],
                'description' => $validated['abstract'] ?? '',
                'resource_type' => $resourceType,
                'subject_area' => $validated['category'],
                'file_path' => $filePath,
                'file_name' => $originalName,
                'file_size' => $fileSize,
                'published_at' => !empty($validated['publication_date']) ? date('Y-m-d H:i:s', strtotime($validated['publication_date'])) : null,
                'is_featured' => $validated['is_featured'] ?? false,
                'access_level' => $validated['access_level'],
                'newsletter_volume' => $validated['newsletter_volume'] ?? null,
                'newsletter_issue' => $validated['newsletter_issue'] ?? null,
                'status' => Others::STATUS_DRAFT,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Handle tags
            if (!empty($validated['tags'])) {
                $this->processTags($document, $validated['tags']);
            }

            // Handle Newspaper Articles
            if ($isNewsletter && $request->has('articles')) {
                foreach ($request->input('articles') as $index => $articleData) {
                    $articleImagePath = null;
                    
                    // Handle image upload for this article
                    if ($request->hasFile("articles.{$index}.image")) {
                        $imageFile = $request->file("articles.{$index}.image");
                        $imageName = 'newsletter_' . time() . '_' . $index . '.' . $imageFile->getClientOriginalExtension();
                        $articleImagePath = $imageFile->storeAs('newsletter_images', $imageName, 'public');
                    }

                    $document->newsletterArticles()->create([
                        'title' => $articleData['title'],
                        'content' => $articleData['content'],
                        'image_path' => $articleImagePath,
                        'sort_order' => $index,
                    ]);
                }
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isNewsletter ? __('Newsletter created successfully') : __('File uploaded successfully'),
                'data' => $document,
            ], 201);

        } catch (\Exception $e) {
            \DB::rollBack();
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            \Log::error('Resource creation failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'type' => $request->input('document_type'),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => __('Failed to process request: ') . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process and attach tags to the document
     */
    private function processTags(Others $document, string $tagsInput): void
    {
        // Parse comma-separated tags and filter empty values
        $tags = array_filter(array_map('trim', explode(',', $tagsInput)));

        // Save tags as JSON array directly in the tags field
        $document->tags = array_values($tags);
        $document->save();
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
            '#6b7280',
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
        $document = Others::findOrFail($id);

        $this->authorize('delete', $document);

        try {
            \DB::beginTransaction();

            // Store document info for logging before deletion
            $documentTitle = $document->title;
            $filePath = $document->file_path;

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
                    'message' => __('File deleted successfully'),
                ]);
            }

            return redirect()->route('admin.others.index')
                ->with('success', __('File deleted successfully'));

        } catch (\Exception $e) {
            \DB::rollBack();

            \Log::error('File deletion failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'document_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to delete file: ') . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('Failed to delete file: ') . $e->getMessage());
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
                    'message' => __('No files selected for deletion'),
                ], 400);
            }
            return redirect()->back()->with('error', __('No files selected for deletion'));
        }

        try {
            \DB::beginTransaction();

            $deletedCount = 0;
            $failedCount = 0;
            $deletedTitles = [];

            foreach ($documentIds as $documentId) {
                $document = Others::find($documentId);

                if (! $document) {
                    $failedCount++;
                    continue;
                }

                // Check authorization for each document
                if (! Auth::user()->can('delete', $document)) {
                    $failedCount++;
                    continue;
                }

                $documentTitle = $document->title;
                $filePath = $document->file_path;

                // Delete associated data
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
                    'failed_count' => $failedCount,
                ]);
            }

            return redirect()->route('admin.others.index')
                ->with($deletedCount > 0 ? 'success' : 'error', trim($message));

        } catch (\Exception $e) {
            \DB::rollBack();

            \Log::error('Bulk document deletion failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'document_ids' => $documentIds,
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to delete documents: ') . $e->getMessage(),
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
        $document = Others::withTrashed()->findOrFail($id);

        $this->authorize('forceDelete', $document);

        try {
            \DB::beginTransaction();

            $documentTitle = $document->title;
            $filePath = $document->file_path;

            // Permanently delete associated data
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
                    'message' => __('File permanently deleted'),
                ]);
            }

            return redirect()->route('admin.others.index')
                ->with('success', __('File permanently deleted'));

        } catch (\Exception $e) {
            \DB::rollBack();

            \Log::error('Force file deletion failed: ' . $e->getMessage());

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to permanently delete file'),
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('Failed to permanently delete file'));
        }
    }

    /**
     * Restore a soft-deleted document
     */
    public function restore($id)
    {
        $document = Others::withTrashed()->findOrFail($id);

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
                    'message' => __('File restored successfully'),
                ]);
            }

            return redirect()->route('admin.others.index')
                ->with('success', __('File restored successfully'));

        } catch (\Exception $e) {
            \Log::error('File restoration failed: ' . $e->getMessage());

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to restore file'),
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('Failed to restore file'));
        }
    }

    /**
     * Change Others resource status (workflow action)
     */
    public function changeStatus(Request $request, $id)
    {
        $document = Others::findOrFail($id);
        $action = $request->input('action');
        $comment = $request->input('comment', '');

        $availableActions = method_exists($document, 'getAvailableActions') ? $document->getAvailableActions() : [];
        if (! isset($availableActions[$action])) {
            return response()->json([
                'success' => false,
                'message' => __('This action is not available for the current file status or you do not have permission.'),
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

            if ($targetStatus === Others::STATUS_PUBLISHED && ! $document->published_at) {
                $updateData['published_at'] = now();
            }
            if ($targetStatus === Others::STATUS_APPROVED) {
                $updateData['approved_by'] = Auth::id();
            }

            $document->update($updateData);

            // Send notifications
            $this->sendStatusChangeNotifications($document, $oldStatus, $targetStatus, $action);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('File status updated successfully'),
                'data' => [
                    'new_status' => $targetStatus,
                    'status_display' => method_exists($document, 'getStatusDisplay') ? $document->getStatusDisplay() : $targetStatus,
                    'status_color' => method_exists($document, 'getStatusColor') ? $document->getStatusColor() : 'gray',
                    'available_actions' => method_exists($document, 'getAvailableActions') ? $document->getAvailableActions() : [],
                ],
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Others status change failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Failed to update file status: ') . $e->getMessage(),
            ], 500);
        }
    }

    public function workflowHistory($id)
    {
        return response()->json([
            'success' => true,
            'data' => [],
        ]);
    }

    /**
     * Send notifications to relevant users based on status change (Others)
     */
    private function sendStatusChangeNotifications(Others $document, $oldStatus, $newStatus, $action)
    {
        $changerName = Auth::user()?->name ?: 'System';
        $notification = new OthersStatusChanged($document, $oldStatus, $newStatus, $action, $changerName);

        $usersToNotify = $this->getUsersToNotify($action, $document);
        foreach ($usersToNotify as $user) {
            $user->notify($notification);
        }

        // Send newsletter notification to subscribers when a newsletter is published
        if ($newStatus === Others::STATUS_PUBLISHED && $document->resource_type === Others::TYPE_NEWSLETTER) {
            event(new NewContentPublished($document, 'newsletters'));
        }
    }

    /**
     * Determine users to notify for Others workflow actions
     */
    private function getUsersToNotify($action, Others $document)
    {
        $permissionMap = [
            'send_for_review' => 'document.approve',
            'approve' => 'document.publish',
            'reject' => 'document.review',
            'publish' => null,
            'unpublish' => 'document.review',
            'archive' => 'document.restore',
            'restore' => 'document.review',
            'send_back' => 'document.review',
        ];

        $requiredPermission = $permissionMap[$action] ?? null;
        if ($requiredPermission) {
            return User::permission($requiredPermission)->get();
        }

        if ($action === 'publish' && $document->created_by) {
            return User::where('id', $document->created_by)->get();
        }

        return User::permission([
            'document.review',
            'document.approve',
            'document.publish',
            'document.unpublish',
        ])->get();
    }

    public function api(Request $request)
    {
        $this->authorize('viewAny', Others::class);

        $result = $this->mediaLibraryService->getMediaList(
            $request->get('search'),
            $request->get('type'),
            $request->get('sort', 'created_at'),
            $request->get('direction', 'desc'),
            (int) $request->get('per_page', 100),
            null,
            'uploads'
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
