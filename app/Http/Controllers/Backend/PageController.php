<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Models\Page;
use App\Models\Term;
use App\Models\User;
use App\Enums\PageStatus;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\PageService;
use App\Enums\Hooks\PageActionHook;
use App\Enums\Hooks\PageFilterHook;
use App\Http\Controllers\Controller;
use App\Notifications\StatusChanged;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Services\Content\ContentService;
use App\Http\Requests\Page\StorePageRequest;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\Page\UpdatePageRequest;
use App\Http\Requests\Common\BulkDeleteRequest;

class PageController extends Controller
{
    public function __construct(
        private readonly ContentService $contentService,
        private readonly PageService $postService,
    ) {
    }

    public function index(Request $request, string $postType = 'page'): RedirectResponse|Renderable
    {
        $this->authorize('viewAny', Page::class);

        // Get post type.
        $pageTypeModel = $this->contentService->getPostType($postType);

        if (!$pageTypeModel) {
            return redirect()->route('admin.pages.index')->with('error', 'Post type not found');
        }

        // Prepare filters
        $filters = [
            'post_type' => $postType,
            'search' => $request->search,
            'status' => $request->status,
            'category' => $request->category,
            'tag' => $request->tag,
        ];

        $this->setBreadcrumbTitle($pageTypeModel->label);

        // Get categories and tags for filters.
        $categories = Term::where('taxonomy', 'category')->select('id', 'name')->get();
        $tags = Term::where('taxonomy', 'tag')->select('id', 'name')->get();

        return $this->renderViewWithBreadcrumbs('backend.pages.pages.index', compact('postType', 'pageTypeModel', 'categories', 'tags'));
    }

    public function create(string $postType = 'page'): RedirectResponse|Renderable
    {
        $this->authorize('create', Page::class);

        // Get post type.
        $pageTypeModel = $this->contentService->getPostType($postType);

        if (!$pageTypeModel) {
            return redirect()->route('admin.pages.index')->with('error', 'Post type not found');
        }

        // Get taxonomies.
        $taxonomies = [];
        if (!empty($pageTypeModel->taxonomies)) {
            $taxonomies = $this->contentService->getTaxonomies()
                ->whereIn('name', $pageTypeModel->taxonomies)
                ->all();
        }

        // Get parent posts for hierarchical post types.
        $parentPosts = [];
        if ($pageTypeModel->hierarchical) {
            $parentPosts = Page::where('post_type', $postType)
                ->pluck('title', 'id')
                ->toArray();
        }

        $this->setBreadcrumbTitle(__('New :postType', ['postType' => $pageTypeModel->label_singular]))
            ->addBreadcrumbItem($pageTypeModel->label, route('admin.pages.index', $postType));

        return $this->renderViewWithBreadcrumbs('backend.pages.pages.create', compact('postType', 'pageTypeModel', 'taxonomies', 'parentPosts'));
    }

    public function store(StorePageRequest $request, string $postType = 'page'): RedirectResponse
    {
        $this->authorize('create', Page::class);

        // Get post type.
        $pageTypeModel = $this->contentService->getPostType($postType);

        if (!$pageTypeModel) {
            return redirect()->route('admin.pages.index')->with('error', 'Post type not found');
        }

        $data = $this->addHooks(
            $request->validated(),
            PageActionHook::PAGE_CREATED_BEFORE,
            PageFilterHook::PAGE_CREATED_BEFORE
        );

        // Create post
        $page = new Page();
        $page->title = $data['title'];
        $page->slug = $data['slug'] ?? Str::slug($data['title']);
        $page->content = $data['content'];
        $page->excerpt = $data['excerpt'] ?? Str::limit(strip_tags($data['content']), 200);
        $page->status = $data['status'] ?? 'created';
        $page->post_type = $postType;
        $page->user_id = Auth::id();

        $page->save();


        $page = $this->addHooks(
            $page,
            PageActionHook::PAGE_CREATED_AFTER,
            PageFilterHook::PAGE_CREATED_AFTER
        );

        // Handle taxonomies
        $this->handleTaxonomies($request, $page);

        session()->flash('success', __('Page has been created.'));
        $users = User::permission('blog.edit')->get();
        Notification::send($users, new StatusChanged($page, "Editable Page: {$page->title}"));

        return redirect()->route('admin.pages.edit', [$postType, $page->id]);
    }

    public function show(string $postType, string $id): Renderable
    {
        $page = Page::where('post_type', $postType)->findOrFail($id);
        $this->authorize('view', $page);
        $pageTypeModel = $this->contentService->getPostType($postType);

        $this->setBreadcrumbTitle(__('View :postName', ['postName' => $page->title]))
            ->addBreadcrumbItem($pageTypeModel->label, route('admin.pages.index', $postType));

        return $this->renderViewWithBreadcrumbs('backend.pages.pages.show', compact('page', 'postType', 'pageTypeModel'));
    }

    public function edit(string $postType, string $id): RedirectResponse|Renderable
    {
        // Get post with postMeta relationship.
        $page = Page::with(['terms'])
            ->where('post_type', $postType)
            ->findOrFail($id);

        $this->authorize('update', $page);

        // Get post type
        $pageTypeModel = $this->contentService->getPostType($postType);

        if (!$pageTypeModel) {
            return redirect()->route('admin.pages.index')->with('error', 'Post type not found');
        }

        // Get taxonomies
        $taxonomies = [];
        if (!empty($pageTypeModel->taxonomies)) {
            $taxonomies = $this->contentService->getTaxonomies()
                ->whereIn('name', $pageTypeModel->taxonomies)
                ->all();
        }

        // Get parent pages for hierarchical post types
        $parentPages = [];
        if ($pageTypeModel->hierarchical) {
            $parentPages = Page::where('post_type', $postType)
                ->where('id', '!=', $id)
                ->pluck('title', 'id')
                ->toArray();
        }

        // Get selected terms
        $selectedTerms = [];
        foreach ($page->terms as $term) {
            $taxonomyName = $term->getAttribute('taxonomy');
            if ($taxonomyName && !isset($selectedTerms[$taxonomyName])) {
                $selectedTerms[$taxonomyName] = [];
            }
            if ($taxonomyName) {
                $selectedTerms[$taxonomyName][] = $term->id;
            }
        }

        $this->setBreadcrumbTitle(__('Edit :postType', ['postType' => $pageTypeModel->label_singular]))
            ->addBreadcrumbItem($pageTypeModel->label, route('admin.posts.index', $postType));

        // Get categories and tags for filters.
        $categories = Term::where('taxonomy', 'category')->select('id', 'name')->get();
        $tags = Term::where('taxonomy', 'tag')->select('id', 'name')->get();

        return $this->renderViewWithBreadcrumbs('backend.pages.posts.edit', compact('page', 'postType', 'pageTypeModel', 'taxonomies', 'parentPages', 'selectedTerms', 'categories', 'tags'));
    }

    public function update(UpdatePageRequest $request, string $postType, string $id): RedirectResponse
    {
        $page = Page::where('post_type', $postType)->findOrFail($id);
        $this->authorize('update', $page);

        $data = $request->validated();

        $page->title = $data['title'];
        $page->slug = $data['slug'] ?? Str::slug($data['title']);
        $page->content = $data['content'];
        $page->excerpt = $data['excerpt'];

        // Auto-change status from 'created' to 'edited' when post is updated
        if ($page->status === 'created') {
            $page->status = 'edited';
        }

        // Handle publish date.
        if (isset($data['schedule_page']) && $data['schedule_page'] && !empty($data['published_at'])) {
            $page->status = PageStatus::SCHEDULED->value;
            $page->published_at = Carbon::parse($data['published_at']);
        } elseif (isset($data['status']) && $data['status'] === PageStatus::SCHEDULED->value && !empty($data['published_at'])) {
            $page->published_at = Carbon::parse($data['published_at']);
        } elseif (isset($data['status']) && $data['status'] === PageStatus::PUBLISHED->value && !$page->published_at) {
            $page->published_at = now();
        }

        $page->save();

        $this->handleTaxonomies($request, $page);

        session()->flash('success', __('Page updated successfully.'));

        return back();
    }

    public function updateStatus(Request $request, string $postType, string $id): RedirectResponse
    {
        $page = Page::where('post_type', $postType)->findOrFail($id);
        $this->authorize('update', $page);
        
        $request->validate([
            'status' => 'required|string|in:created,edited,approved,published,unpublished,archived'
        ]);
        
        $page->update(['status' => $request->status]);
        
        session()->flash('success', __('Page status updated successfully.'));
        
        return back();
    }

    protected function handleTaxonomies(Request $request, Page $page)
    {
        // Get current post type.
        $pageTypeModel = $this->contentService->getPostType($page->post_type);

        if (!$pageTypeModel || empty($pageTypeModel->taxonomies)) {
            return;
        }

        // Initialize empty arrays for each taxonomy.
        $termIds = [];
        foreach ($pageTypeModel->taxonomies as $taxonomy) {
            $termKey = 'taxonomy_' . $taxonomy;
            if ($request->has($termKey)) {
                $taxonomyTerms = $request->input($termKey);
                if (is_array($taxonomyTerms)) {
                    $termIds = array_merge($termIds, $taxonomyTerms);
                }
            }
        }

        // Sync terms.
        $page->terms()->sync($termIds);

        $this->addHooks(
            ['page' => $page, 'term_ids' => $termIds],
            PageActionHook::PAGE_TAXONOMIES_UPDATED
        );
    }


    /**
 * Change news status (workflow action)
 */
    public function changeStatus(Request $request, $id)
    {
        $page = Page::findOrFail($id);
        $action = $request->input('action');
        $comment = $request->input('comment', '');

        // Check if user can perform this action
        if (!$page->canPerformAction($action)) {
            return response()->json([
                'success' => false,
                'message' => __('You do not have permission to perform this action.')
            ], 403);
        }

        $availableActions = $page->getAvailableActions();
        $targetStatus = $availableActions[$action]['target'];

        try {
            \DB::beginTransaction();

            $oldStatus = $page->status;
            
            // Update post status
            $updateData = [
                'status' => $targetStatus,
            ];

            // Set published_at if publishing
            if ($targetStatus === Page::STATUS_PUBLISHED && !$page->published_at) {
                $updateData['published_at'] = now();
            }

            $page->update($updateData);

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