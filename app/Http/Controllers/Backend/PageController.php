<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Enums\Hooks\PageActionHook;
use App\Enums\Hooks\PageFilterHook;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BulkDeleteRequest;
use App\Models\Page;
use App\Http\Requests\Page\StorePageRequest;
use App\Http\Requests\Page\UpdatePageRequest;
use App\Support\Facades\Hook;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Services\PageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    public function __construct(
        private readonly PageService $pageService,
    ) {
    }

    public function index(): Renderable
    {
        $this->authorize('viewAny', Page::class);

        $breadcrumbs = [
            'title' => __('Pages'),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Pages'),
                    'url' => '#',
                ],
            ],
        ];

        // Get statistics for the dashboard
        $stats = [
            'total' => Page::count(),
            'published' => Page::where('status', Page::STATUS_PUBLISHED)->count(),
            'draft' => Page::where('status', Page::STATUS_DRAFT)->count(),
            'archived' => Page::where('status', Page::STATUS_ARCHIVED)->count(),
        ];

        return view('backend.pages.pages.index', compact('breadcrumbs', 'stats'));
    }

    public function create(): Renderable
    {
        $this->authorize('create', Page::class);

        $this->setBreadcrumbTitle(__('New Page'))
            ->addBreadcrumbItem("Pages", route('admin.pages.index'));

        return $this->renderViewWithBreadcrumbs('backend.pages.pages.create');
    }

    public function store(StorePageRequest $storePageRequest): RedirectResponse
    {
        $this->authorize('create', Page::class);

        $data = $this->addHooks(
            $storePageRequest->validated(),
            PageActionHook::PAGE_CREATED_BEFORE,
            PageFilterHook::PAGE_CREATED_BEFORE
        );

        // Create Page using the service
        $page = $this->pageService->createPage(array_merge($data, [
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]));

        $page = $this->addHooks(
            $page,
            PageActionHook::PAGE_CREATED_AFTER,
            PageFilterHook::PAGE_CREATED_AFTER
        );

        return redirect()->route('admin.pages.index')->with('success', __('Page created successfully.'));
    }

    public function edit(Page $page): Renderable
    {
        $this->authorize('update', $page);

        $this->setBreadcrumbTitle(__('Edit Page'))
            ->addBreadcrumbItem("Pages", route('admin.pages.index'));

        return $this->renderViewWithBreadcrumbs('backend.pages.pages.edit', compact('page'));
    }

    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $this->authorize('update', $page);

        $data = $this->addHooks(
            $request->validated(),
            PageActionHook::PAGE_UPDATED_BEFORE,
            PageFilterHook::PAGE_UPDATED_BEFORE
        );

        // Update page using the service
        $this->pageService->updatePage($page, array_merge($data, [
            'updated_by' => Auth::id(),
        ]));

        $page = $this->addHooks(
            $page,
            PageActionHook::PAGE_UPDATED_AFTER,
            PageFilterHook::PAGE_UPDATED_AFTER
        );

        return redirect()->route('admin.pages.index')
            ->with('success', __('Page updated successfully.'));
    }

    public function destroy(Page $page): RedirectResponse
    {
        $this->authorize('delete', $page);

        Hook::doAction(PageActionHook::PAGE_DELETED_BEFORE, $page);

        $this->pageService->deletePage($page);

        Hook::doAction(PageActionHook::PAGE_DELETED_AFTER, $page);

        return redirect()
            ->route('admin.pages.index')
            ->with('success', __('Page deleted successfully.'));
    }

    public function show(string $id): Renderable
    {
        $page = Page::with(['createdBy'])->findOrFail($id);

        $this->authorize('view', $page);

        $breadcrumbs = [
            ['name' => __('Pages'), 'url' => route('admin.pages.index')],
            ['name' => __('View Page')],
        ];
        
        $this->setBreadcrumbTitle(__('View :pageName', ['pageName' => $page->title]))
            ->addBreadcrumbItem(__('Pages'), route('admin.pages.index'));

        return $this->renderViewWithBreadcrumbs('backend.pages.pages.show', compact('page', 'breadcrumbs'));
    }

    public function bulkDelete(BulkDeleteRequest $request): RedirectResponse
    {
        $this->authorize('bulkDelete', Page::class);

        $ids = $request->validated('ids');

        if (empty($ids)) {
            session()->flash('error', __('No pages selected for deletion.'));
            return redirect()->route('admin.pages.index');
        }

        $ids = $this->addHooks(
            $ids,
            PageActionHook::PAGE_BULK_DELETED_BEFORE
        );

        $deletedCount = $this->pageService->bulkDeletePages($ids);

        $this->addHooks(
            ['deleted_count' => $deletedCount, 'post_type' => 'page'],
            PageActionHook::PAGE_BULK_DELETED_AFTER
        );

        if ($deletedCount > 0) {
            session()->flash('success', __(':count pages deleted successfully', ['count' => $deletedCount]));
        } else {
            session()->flash('error', __('No pages were deleted.'));
        }

        return redirect()->route('admin.pages.index');
    }

    public function changeStatus(Request $request, $page): JsonResponse
    {
        $this->authorize('update', $page);
        // Handle both route model binding and ID parameter
        if (is_numeric($page)) {
            $page = Page::find($page);
        }
        
        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found'
            ], 404);
        }

        $request->validate([
            'status' => 'required|in:published,draft,archived'
        ]);

        try {
            $this->authorize('update', $page);
            
            $this->pageService->updatePageStatus($page, $request->status, auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Page status updated successfully',
                'status' => $page->fresh()->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating page status: ' . $e->getMessage()
            ], 500);
        }
    }
}