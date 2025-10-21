<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Enums\Hooks\PostActionHook;
use App\Enums\Hooks\PostFilterHook;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Requests\Common\BulkDeleteRequest;
use App\Models\News;
use App\Models\Post;
use App\Models\Term;
use App\Services\Content\ContentService;
use App\Services\ImageService;
use App\Services\MediaLibraryService;
use App\Services\NotificationService;
use App\Services\PostMetaService;
use App\Services\PostService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function __construct(
        private readonly ContentService $contentService,
        private readonly PostMetaService $postMetaService,
        private readonly PostService $postService,
        private readonly ImageService $imageService,
        private readonly MediaLibraryService $mediaService,
        private readonly NotificationService $notificationService
    ) {
    }

    public function index(Request $request, string $newsType = 'news'): RedirectResponse|Renderable
    {
        $this->authorize('viewAny', Post::class);

        $postTypeModel = $this->contentService->getPostType($newsType);

        if (! $postTypeModel) {
            return redirect()->route('admin.news.index')->with('error', 'News type not found');
        }

        $this->setBreadcrumbTitle($postTypeModel->label);

        $categories = Term::where('taxonomy', 'category')->select('id', 'name')->get();
        $tags = Term::where('taxonomy', 'tag')->select('id', 'name')->get();

        $postType = $newsType;
        return $this->renderViewWithBreadcrumbs('backend.pages.posts.index', compact('postType', 'postTypeModel', 'categories', 'tags'));
    }

    public function create(string $newsType = 'news'): RedirectResponse|Renderable
    {
        $this->authorize('create', Post::class);

        $postTypeModel = $this->contentService->getPostType($newsType);

        if (! $postTypeModel) {
            return redirect()->route('admin.news.index')->with('error', 'News type not found');
        }

        $taxonomies = [];
        if (! empty($postTypeModel->taxonomies)) {
            $taxonomies = $this->contentService->getTaxonomies()
                ->whereIn('name', $postTypeModel->taxonomies)
                ->all();
        }

        $parentPosts = [];
        if ($postTypeModel->hierarchical) {
            $parentPosts = News::pluck('title', 'id')->toArray();
        }

        $this->setBreadcrumbTitle(__('New :postType', ['postType' => $postTypeModel->label_singular]))
            ->addBreadcrumbItem($postTypeModel->label, route('admin.news.index', $newsType));

        $postType = $newsType;
        return $this->renderViewWithBreadcrumbs('backend.pages.news.create', compact('postType', 'postTypeModel', 'taxonomies', 'parentPosts'));
    }

    public function store(StorePostRequest $request, string $newsType = 'news'): RedirectResponse
    {
        $this->authorize('create', Post::class);

        $postTypeModel = $this->contentService->getPostType($newsType);

        if (! $postTypeModel) {
            return redirect()->route('admin.news.index')->with('error', 'News type not found');
        }

        $data = $this->addHooks(
            $request->validated(),
            PostActionHook::POST_CREATED_BEFORE,
            PostFilterHook::POST_CREATED_BEFORE
        );

        $post = new News();
        $post->title = $data['title'];
        $post->slug = $data['slug'] ?? Str::slug($data['title']);
        $post->content = $data['content'];
        $post->excerpt = $data['excerpt'] ?? Str::limit(strip_tags($data['content']), 200);
        $post->status = $data['status'] ?? 'draft';
        $post->post_type = $newsType;
        $post->user_id = Auth::id();
        $post->parent_id = $data['parent_id'] ?? null;

        // News-specific fields
        if (isset($data['author'])) {
            $post->setMeta('author', $data['author']);
        }
        if (isset($data['news_source'])) {
            $post->setMeta('news_source', $data['news_source']);
        }
        if (isset($data['priority'])) {
            $post->setMeta('priority', $data['priority']);
        }
        if (isset($data['published_date'])) {
            $post->published_at = $data['published_date'];
        }

        $post->save();

        // Create notification for new news
        try {
            $this->notificationService->createNewsNotification($post);
            \Log::info('News notification created for: ' . $post->title);
        } catch (\Exception $e) {
            \Log::error('Failed to create news notification: ' . $e->getMessage());
        }

        if (isset($data['remove_featured_image']) && $data['remove_featured_image']) {
            $post->clearMediaCollection('featured');
        } elseif (! empty($data['featured_image'])) {
            if ($request->hasFile('featured_image')) {
                $post->clearMediaCollection('featured');
                $post->addMediaFromRequest('featured_image')->toMediaCollection('featured');
            } else {
                $this->mediaService->associateExistingMedia($post, $data['featured_image'], 'featured');
            }
        }

        $post = $this->addHooks(
            $post,
            PostActionHook::POST_CREATED_AFTER,
            PostFilterHook::POST_CREATED_AFTER
        );

        session()->flash('success', __('News has been created.'));

        return redirect()->route('admin.news.edit', [$newsType, $post->id]);
    }

    public function show(string $newsType, string $id): Renderable
    {
        $post = News::findOrFail($id);
        $this->authorize('view', $post);
        $postTypeModel = $this->contentService->getPostType($newsType);

        $this->setBreadcrumbTitle(__('View :postName', ['postName' => $post->title]))
            ->addBreadcrumbItem($postTypeModel->label, route('admin.news.index', $newsType));

        $postType = $newsType;
        return $this->renderViewWithBreadcrumbs('backend.pages.posts.show', compact('post', 'postType', 'postTypeModel'));
    }

    public function edit(string $newsType, string $id): RedirectResponse|Renderable
    {
        $post = News::with(['postMeta', 'terms'])->findOrFail($id);

        $this->authorize('update', $post);

        $postTypeModel = $this->contentService->getPostType($newsType);

        if (! $postTypeModel) {
            return redirect()->route('admin.news.index')->with('error', 'News type not found');
        }

        $taxonomies = [];
        if (! empty($postTypeModel->taxonomies)) {
            $taxonomies = $this->contentService->getTaxonomies()
                ->whereIn('name', $postTypeModel->taxonomies)
                ->all();
        }

        $parentPosts = [];
        if ($postTypeModel->hierarchical) {
            $parentPosts = News::where('id', '!=', $id)
                ->pluck('title', 'id')
                ->toArray();
        }

        $selectedTerms = [];
        foreach ($post->terms as $term) {
            $taxonomyName = $term->getAttribute('taxonomy');
            if ($taxonomyName && ! isset($selectedTerms[$taxonomyName])) {
                $selectedTerms[$taxonomyName] = [];
            }
            if ($taxonomyName) {
                $selectedTerms[$taxonomyName][] = $term->id;
            }
        }

        $this->setBreadcrumbTitle(__('Edit :postType', ['postType' => $postTypeModel->label_singular]))
            ->addBreadcrumbItem($postTypeModel->label, route('admin.news.index', $newsType));

        $postType = $newsType;
        return $this->renderViewWithBreadcrumbs('backend.pages.news.edit', compact('post', 'postType', 'postTypeModel', 'taxonomies', 'parentPosts', 'selectedTerms'));
    }

    public function update(UpdatePostRequest $request, string $newsType, string $id): RedirectResponse
    {
        $post = News::findOrFail($id);
        $this->authorize('update', $post);

        $data = $this->addHooks(
            $request->validated(),
            PostActionHook::POST_UPDATED_BEFORE,
            PostFilterHook::POST_UPDATED_BEFORE
        );

        $oldStatus = $post->status;
        
        $post->title = $data['title'];
        $post->slug = $data['slug'] ?? Str::slug($data['title']);
        $post->content = $data['content'];
        $post->excerpt = $data['excerpt'] ?? Str::limit(strip_tags($data['content']), 200);
        $post->status = $data['status'];
        $post->parent_id = $data['parent_id'] ?? null;
        
        // Check if status changed
        $statusChanged = $oldStatus !== $data['status'];

        // News-specific fields
        if (isset($data['author'])) {
            $post->setMeta('author', $data['author']);
        }
        if (isset($data['news_source'])) {
            $post->setMeta('news_source', $data['news_source']);
        }
        if (isset($data['priority'])) {
            $post->setMeta('priority', $data['priority']);
        }
        if (isset($data['published_date'])) {
            $post->published_at = $data['published_date'];
        }

        $post->save();

        // Create notification if status changed
        if ($statusChanged) {
            $this->notificationService->createStatusChangeNotification($post, $oldStatus, $data['status']);
        }

        if (isset($data['remove_featured_image']) && $data['remove_featured_image']) {
            $post->clearMediaCollection('featured');
        } elseif (! empty($data['featured_image'])) {
            $post->clearMediaCollection('featured');

            if ($request->hasFile('featured_image')) {
                $post->addMediaFromRequest('featured_image')->toMediaCollection('featured');
            } else {
                $this->mediaService->associateExistingMedia($post, $data['featured_image'], 'featured');
            }
        }

        $post = $this->addHooks(
            $post,
            PostActionHook::POST_UPDATED_AFTER,
            PostFilterHook::POST_UPDATED_AFTER
        );

        session()->flash('success', __('News has been updated.'));

        return back();
    }

    public function destroy(string $newsType, string $id): RedirectResponse
    {
        $post = News::findOrFail($id);
        $this->authorize('delete', $post);

        $post = $this->addHooks(
            $post,
            PostActionHook::POST_DELETED_BEFORE,
            PostFilterHook::POST_DELETED_BEFORE
        );

        $post->delete();

        $this->addHooks(
            $post,
            PostActionHook::POST_DELETED_AFTER,
            PostFilterHook::POST_DELETED_AFTER
        );

        session()->flash('success', __('News has been deleted.'));

        return redirect()->route('admin.news.index', $newsType);
    }

    public function bulkDelete(BulkDeleteRequest $request, string $newsType): RedirectResponse
    {
        $this->authorize('bulkDelete', Post::class);

        $ids = $request->validated('ids');

        if (empty($ids)) {
            session()->flash('error', __('No news selected for deletion.'));
            return redirect()->route('admin.news.index', $newsType);
        }

        $ids = $this->addHooks(
            $ids,
            PostActionHook::POST_BULK_DELETED_BEFORE
        );

        $deletedCount = $this->postService->bulkDeletePosts($ids, $newsType);

        $this->addHooks(
            ['deleted_count' => $deletedCount, 'post_type' => $newsType],
            PostActionHook::POST_BULK_DELETED_AFTER
        );

        if ($deletedCount > 0) {
            session()->flash('success', __(':count news deleted successfully', ['count' => $deletedCount]));
        } else {
            session()->flash('error', __('No news were deleted.'));
        }

        return redirect()->route('admin.news.index', $newsType);
    }
}