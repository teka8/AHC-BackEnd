<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Enums\Hooks\PostActionHook;
use App\Enums\Hooks\PostFilterHook;
use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Requests\Common\BulkDeleteRequest;
use App\Models\Post;
use App\Models\Term;
use App\Services\Content\ContentService;
use App\Services\ImageService;
use App\Services\MediaLibraryService;
use App\Services\PostMetaService;
use App\Services\PostService;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EventController extends Controller
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

        if (! $postTypeModel) {
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

        if (! $postTypeModel) {
            return redirect()->route('admin.posts.index')->with('error', 'Post type not found');
        }

        // Get taxonomies.
        $taxonomies = [];
        if (! empty($postTypeModel->taxonomies)) {
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
}
