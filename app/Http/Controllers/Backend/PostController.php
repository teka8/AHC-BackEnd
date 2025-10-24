<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\Term;
use App\Models\User;
use App\Enums\PostStatus;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\PostService;
use App\Services\ImageService;
use App\Services\PostMetaService;
use App\Enums\Hooks\PostActionHook;
use App\Enums\Hooks\PostFilterHook;
use App\Http\Controllers\Controller;
use App\Notifications\StatusChanged;
use Illuminate\Support\Facades\Auth;
use App\Services\MediaLibraryService;
use Illuminate\Http\RedirectResponse;
use App\Services\Content\ContentService;
use App\Http\Requests\Post\StorePostRequest;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Requests\Common\BulkDeleteRequest;

class PostController extends Controller
{
    public function __construct(
        private readonly ContentService $contentService,
        private readonly PostMetaService $postMetaService,
        private readonly PostService $postService,
        private readonly ImageService $imageService,
        private readonly MediaLibraryService $mediaService
    ) {
    }

    public function index(Request $request, string $postType = 'post'): RedirectResponse|Renderable
    {
        $this->authorize('viewAny', Post::class);

        // Get post type.
        $postTypeModel = $this->contentService->getPostType($postType);

        if (!$postTypeModel) {
            return redirect()->route('admin.posts.index')->with('error', 'Post type not found');
        }

        // Prepare filters
        $filters = [
            'post_type' => $postType,
            'search' => $request->search,
            'status' => $request->status,
            'category' => $request->category,
            'tag' => $request->tag,
        ];

        $this->setBreadcrumbTitle($postTypeModel->label);

        // Get categories and tags for filters.
        $categories = Term::where('taxonomy', 'category')->select('id', 'name')->get();
        $tags = Term::where('taxonomy', 'tag')->select('id', 'name')->get();

        return $this->renderViewWithBreadcrumbs('backend.pages.posts.index', compact('postType', 'postTypeModel', 'categories', 'tags'));
    }

    public function create(string $postType = 'post'): RedirectResponse|Renderable
    {
        $this->authorize('create', Post::class);

        // Get post type.
        $postTypeModel = $this->contentService->getPostType($postType);

        if (!$postTypeModel) {
            return redirect()->route('admin.posts.index')->with('error', 'Post type not found');
        }

        // Get taxonomies.
        $taxonomies = [];
        if (!empty($postTypeModel->taxonomies)) {
            $taxonomies = $this->contentService->getTaxonomies()
                ->whereIn('name', $postTypeModel->taxonomies)
                ->all();
        }

        // Get parent posts for hierarchical post types.
        $parentPosts = [];
        if ($postTypeModel->hierarchical) {
            $parentPosts = Post::where('post_type', $postType)
                ->pluck('title', 'id')
                ->toArray();
        }

        $this->setBreadcrumbTitle(__('New :postType', ['postType' => $postTypeModel->label_singular]))
            ->addBreadcrumbItem($postTypeModel->label, route('admin.posts.index', $postType));

        return $this->renderViewWithBreadcrumbs('backend.pages.posts.create', compact('postType', 'postTypeModel', 'taxonomies', 'parentPosts'));
    }

    public function store(StorePostRequest $request, string $postType = 'post'): RedirectResponse
    {
        $this->authorize('create', Post::class);

        // Get post type.
        $postTypeModel = $this->contentService->getPostType($postType);

        if (!$postTypeModel) {
            return redirect()->route('admin.posts.index')->with('error', 'Post type not found');
        }

        $data = $this->addHooks(
            $request->validated(),
            PostActionHook::POST_CREATED_BEFORE,
            PostFilterHook::POST_CREATED_BEFORE
        );

        // Create post
        $post = new Post();
        $post->title = $data['title'];
        $post->slug = $data['slug'] ?? Str::slug($data['title']);
        $post->content = $data['content'];
        $post->excerpt = $data['excerpt'] ?? Str::limit(strip_tags($data['content']), 200);
        $post->status = $data['status'] ?? 'created';
        $post->post_type = $postType;
        $post->user_id = Auth::id();
        $post->parent_id = $data['parent_id'] ?? null;

        $post->save();

        // Handle featured image removal first.
        if (isset($data['remove_featured_image']) && $data['remove_featured_image']) {
            $post->clearMediaCollection('featured');
        } elseif (!empty($data['featured_image'])) {
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

        // Handle post meta.
        $this->handlePostMeta($request, $post);

        // Handle taxonomies
        $this->handleTaxonomies($request, $post);

        session()->flash('success', __('News has been created.'));
        $users = User::permission('blog.edit')->get();
        Notification::send($users, new StatusChanged($post, "Editable News: {$post->title}"));

        return redirect()->route('admin.posts.edit', [$postType, $post->id]);
    }

    public function show(string $postType, string $id): Renderable
    {
        $post = Post::where('post_type', $postType)->findOrFail($id);
        $this->authorize('view', $post);
        $postTypeModel = $this->contentService->getPostType($postType);

        $this->setBreadcrumbTitle(__('View :postName', ['postName' => $post->title]))
            ->addBreadcrumbItem($postTypeModel->label, route('admin.posts.index', $postType));

        return $this->renderViewWithBreadcrumbs('backend.pages.posts.show', compact('post', 'postType', 'postTypeModel'));
    }

    public function edit(string $postType, string $id): RedirectResponse|Renderable
    {
        // Get post with postMeta relationship.
        $post = Post::with(['postMeta', 'terms'])
            ->where('post_type', $postType)
            ->findOrFail($id);

        $this->authorize('update', $post);

        // Get post type
        $postTypeModel = $this->contentService->getPostType($postType);

        if (!$postTypeModel) {
            return redirect()->route('admin.posts.index')->with('error', 'Post type not found');
        }

        // Get taxonomies
        $taxonomies = [];
        if (!empty($postTypeModel->taxonomies)) {
            $taxonomies = $this->contentService->getTaxonomies()
                ->whereIn('name', $postTypeModel->taxonomies)
                ->all();
        }

        // Get parent posts for hierarchical post types
        $parentPosts = [];
        if ($postTypeModel->hierarchical) {
            $parentPosts = Post::where('post_type', $postType)
                ->where('id', '!=', $id)
                ->pluck('title', 'id')
                ->toArray();
        }

        // Get selected terms
        $selectedTerms = [];
        foreach ($post->terms as $term) {
            $taxonomyName = $term->getAttribute('taxonomy');
            if ($taxonomyName && !isset($selectedTerms[$taxonomyName])) {
                $selectedTerms[$taxonomyName] = [];
            }
            if ($taxonomyName) {
                $selectedTerms[$taxonomyName][] = $term->id;
            }
        }

        $this->setBreadcrumbTitle(__('Edit :postType', ['postType' => $postTypeModel->label_singular]))
            ->addBreadcrumbItem($postTypeModel->label, route('admin.posts.index', $postType));

        // Get categories and tags for filters.
        $categories = Term::where('taxonomy', 'category')->select('id', 'name')->get();
        $tags = Term::where('taxonomy', 'tag')->select('id', 'name')->get();

        return $this->renderViewWithBreadcrumbs('backend.pages.posts.edit', compact('post', 'postType', 'postTypeModel', 'taxonomies', 'parentPosts', 'selectedTerms', 'categories', 'tags'));
    }

    public function update(UpdatePostRequest $request, string $postType, string $id): RedirectResponse
    {
        $post = Post::where('post_type', $postType)->findOrFail($id);
        $this->authorize('update', $post);

        $data = $request->validated();

        $post->title = $data['title'];
        $post->slug = $data['slug'] ?? Str::slug($data['title']);
        $post->content = $data['content'];
        $post->excerpt = $data['excerpt'];
        $post->parent_id = $data['parent_id'] ?? null;

        // Auto-change status from 'created' to 'edited' when post is updated
        if ($post->status === 'created') {
            $post->status = 'edited';
        }

        // Handle publish date.
        if (isset($data['schedule_post']) && $data['schedule_post'] && !empty($data['published_at'])) {
            $post->status = PostStatus::SCHEDULED->value;
            $post->published_at = Carbon::parse($data['published_at']);
        } elseif (isset($data['status']) && $data['status'] === PostStatus::SCHEDULED->value && !empty($data['published_at'])) {
            $post->published_at = Carbon::parse($data['published_at']);
        } elseif (isset($data['status']) && $data['status'] === PostStatus::PUBLISHED->value && !$post->published_at) {
            $post->published_at = now();
        }

        $post->save();

        $this->handlePostMeta($request, $post);
        $this->handleTaxonomies($request, $post);

        session()->flash('success', __('Post updated successfully.'));

        return back();
    }

    public function updateStatus(Request $request, string $postType, string $id): RedirectResponse
    {
        $post = Post::where('post_type', $postType)->findOrFail($id);
        $this->authorize('update', $post);
        
        $request->validate([
            'status' => 'required|string|in:created,edited,approved,published,unpublished,archived'
        ]);
        
        $post->update(['status' => $request->status]);
        
        session()->flash('success', __('Post status updated successfully.'));
        
        return back();
    }

    protected function handleTaxonomies(Request $request, Post $post)
    {
        // Get current post type.
        $postTypeModel = $this->contentService->getPostType($post->post_type);

        if (!$postTypeModel || empty($postTypeModel->taxonomies)) {
            return;
        }

        // Initialize empty arrays for each taxonomy.
        $termIds = [];
        foreach ($postTypeModel->taxonomies as $taxonomy) {
            $termKey = 'taxonomy_' . $taxonomy;
            if ($request->has($termKey)) {
                $taxonomyTerms = $request->input($termKey);
                if (is_array($taxonomyTerms)) {
                    $termIds = array_merge($termIds, $taxonomyTerms);
                }
            }
        }

        // Sync terms.
        $post->terms()->sync($termIds);

        $this->addHooks(
            ['post' => $post, 'term_ids' => $termIds],
            PostActionHook::POST_TAXONOMIES_UPDATED
        );
    }

    protected function handlePostMeta(Request $request, Post $post)
    {
        $metaKeys = $request->input('meta_keys', []);
        $metaValues = $request->input('meta_values', []);
        $metaTypes = $request->input('meta_types', []);
        $metaDefaultValues = $request->input('meta_default_values', []);

        // Clear existing meta for this post.
        $post->postMeta()->delete();

        // Add new meta.
        foreach ($metaKeys as $index => $key) {
            if (!empty($key) && isset($metaValues[$index])) {
                $this->postMetaService->setMeta(
                    $post->id,
                    $key,
                    $metaValues[$index],
                    $metaTypes[$index] ?? 'input',
                    $metaDefaultValues[$index] ?? null
                );
            }
        }

        $this->addHooks(
            [
                'post' => $post,
                'meta_keys' => $metaKeys,
                'meta_values' => $metaValues,
                'meta_types' => $metaTypes,
                'meta_default_values' => $metaDefaultValues,
            ],
            PostActionHook::POST_META_UPDATED
        );
    }

    /**
 * Change news status (workflow action)
 */
public function changeStatus(Request $request, $id)
{
    $post = Post::findOrFail($id);
    $action = $request->input('action');
    $comment = $request->input('comment', '');

    // Check if user can perform this action
    if (!$post->canPerformAction($action)) {
        return response()->json([
            'success' => false,
            'message' => __('You do not have permission to perform this action.')
        ], 403);
    }

    $availableActions = $post->getAvailableActions();
    $targetStatus = $availableActions[$action]['target'];

    try {
        \DB::beginTransaction();

        $oldStatus = $post->status;
        
        // Update post status
        $updateData = [
            'status' => $targetStatus,
        ];

        // Set published_at if publishing
        if ($targetStatus === Post::STATUS_PUBLISHED && !$post->published_at) {
            $updateData['published_at'] = now();
        }

        $post->update($updateData);

        // Log the status change
        //$this->postService->logStatusChange($post, $oldStatus, $targetStatus, Auth::id(), $comment);

        \DB::commit();

        return response()->json([
            'success' => true,
            'message' => __('News status updated successfully'),
        ]);

    } catch (\Exception $e) {
        \DB::rollBack();
        
        \Log::error('News status change failed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => __('Failed to update news status: ') . $e->getMessage()
        ], 500);
    }
}
}