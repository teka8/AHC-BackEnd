<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Term\StoreTermRequest;
use App\Http\Requests\Term\UpdateTermRequest;
use App\Models\Term;
use App\Services\Content\ContentService;
use App\Services\TermService;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function __construct(
        private readonly ContentService $contentService,
        private readonly TermService $termService
    ) {
    }

    public function index(Request $request, string $taxonomy)
    {
        $this->authorize('viewAny', Term::class);

        // Get taxonomy using service
        $taxonomyModel = $this->termService->getTaxonomy($taxonomy);

        if (! $taxonomyModel) {
            return redirect()->route('admin.posts.index')->with('error', __('Taxonomy not found'));
        }

        $availablePostTypes = $this->contentService->getPostTypes();
        $defaultPostType = $availablePostTypes->has('announcement')
            ? 'announcement'
            : ($availablePostTypes->keys()->first() ?? 'news');
        $postType = strtolower((string) $request->query('post_type', $defaultPostType));
        $postTypeModel = $this->contentService->getPostType($postType) ?? $this->contentService->getPostType($defaultPostType);
        if (! $postTypeModel) {
            $postType = $defaultPostType;
        }

        // Get parent terms for hierarchical taxonomies.
        $parentTerms = [];
        if ($taxonomyModel->hierarchical) {
            $parentTerms = Term::where('taxonomy', $taxonomy)
                ->forPostType($postType)
                ->forPostType($postType)
                ->orderBy('name', 'asc')
                ->get();
        }

        // Get term being edited if exists.
        $term = null;
        if ($request->has('edit') && is_numeric($request->edit)) {
            $term = Term::findOrFail($request->edit);
        }

        $this->setBreadcrumbTitle($taxonomyModel->label);

        return $this->renderViewWithBreadcrumbs('backend.pages.terms.index', [
            'taxonomy' => $taxonomy,
            'taxonomyModel' => $taxonomyModel,
            'parentTerms' => $parentTerms,
            'term' => $term,
            'postType' => $postType,
            'postTypeModel' => $postTypeModel,
            'availablePostTypes' => $availablePostTypes,
        ]);
    }

    public function store(StoreTermRequest $request, string $taxonomy)
    {
        $this->authorize('create', Term::class);
        // Get taxonomy using service
        $taxonomyModel = $this->termService->getTaxonomy($taxonomy);

        if (! $taxonomyModel) {
            return redirect()->route('admin.posts.index')->with('error', __('Taxonomy not found'));
        }

        // Create term using service
        $term = $this->termService->createTerm($request->validated(), $taxonomy);

        // Get taxonomy label for message
        $taxLabel = $this->termService->getTaxonomyLabel($taxonomy, true);

        $postType = strtolower((string) $request->input('context_post_type', $request->query('post_type', 'news')));

        return redirect()->route('admin.terms.index', [
            'taxonomy' => $taxonomy,
            'post_type' => $postType,
        ])
            ->with('success', __(':taxLabel created successfully', ['taxLabel' => $taxLabel]));
    }

    public function update(UpdateTermRequest $request, string $taxonomy, string $id)
    {
        // Get taxonomy using service
        $taxonomyModel = $this->termService->getTaxonomy($taxonomy);

        if (! $taxonomyModel) {
            return redirect()->route('admin.posts.index')->with('error', __('Taxonomy not found'));
        }

        // Get term using service
        $term = $this->termService->getTermById((int) $id, $taxonomy);

        $this->authorize('update', $term);

        // Update term using service
        $postTypeContext = strtolower((string) $request->input('context_post_type', $request->query('post_type')));

        if ($term->isProtectedForPostType($postTypeContext)) {
            return redirect()->route('admin.terms.index', [
                'taxonomy' => $taxonomy,
                'post_type' => $postTypeContext ?: 'announcement',
            ])->with('error', __('This category is protected and cannot be modified.'));
        }

        $this->termService->updateTerm($term, $request->validated());

        // Get taxonomy label for message
        $taxLabel = $this->termService->getTaxonomyLabel($taxonomy, true);

        $postType = $postTypeContext ?: 'announcement';

        return redirect()->route('admin.terms.index', [
            'taxonomy' => $taxonomy,
            'post_type' => $postType,
        ])
            ->with('success', __(':taxLabel updated successfully', ['taxLabel' => $taxLabel]));
    }

    public function destroy(Request $request, string $taxonomy, string $id)
    {
        // Get taxonomy using service
        $taxonomyModel = $this->termService->getTaxonomy($taxonomy);

        if (! $taxonomyModel) {
            return redirect()->route('admin.posts.index')->with('error', __('Taxonomy not found'));
        }

        // Get term using service
        $term = $this->termService->getTermById((int) $id, $taxonomy);

        $this->authorize('delete', $term);

        $postTypeContext = strtolower((string) $request->input('context_post_type', $request->query('post_type')));

        if ($term->isProtectedForPostType($postTypeContext)) {
            return redirect()->route('admin.terms.index', [
                'taxonomy' => $taxonomy,
                'post_type' => $postTypeContext ?: 'announcement',
            ])->with('error', __('This category is protected and cannot be deleted.'));
        }

        // Get taxonomy label for messages
        $taxLabel = $this->termService->getTaxonomyLabel($taxonomy, true);

        // Check if term can be deleted
        $errors = $this->termService->canDeleteTerm($term);

        if (in_array('has_posts', $errors)) {
            return redirect()->route('admin.terms.index', $taxonomy)
                ->with('error', __('Cannot delete :taxLabel as it is associated with posts', ['taxLabel' => $taxLabel]));
        }

        if (in_array('has_children', $errors)) {
            return redirect()->route('admin.terms.index', $taxonomy)
                ->with('error', __('Cannot delete :taxLabel as it has child items', ['taxLabel' => $taxLabel]));
        }

        // Delete term using service
        $this->termService->deleteTerm($term);

        $postType = $postTypeContext ?: 'announcement';

        return redirect()->route('admin.terms.index', [
            'taxonomy' => $taxonomy,
            'post_type' => $postType,
        ])
            ->with('success', __(':taxLabel deleted successfully', ['taxLabel' => $taxLabel]));
    }

    public function edit(string $taxonomy, string $term)
    {
        // Get taxonomy using service.
        $taxonomyModel = $this->termService->getTaxonomy($taxonomy);

        if (! $taxonomyModel) {
            return redirect()->route('admin.posts.index')->with('error', __('Taxonomy not found'));
        }

        // Get term using service.
        $term = $this->termService->getTermById((int) $term, $taxonomy);

        $this->authorize('update', $term);

        // Get parent terms for hierarchical taxonomies.
        $parentTerms = [];
        if ($taxonomyModel->hierarchical) {
            $currentPostType = strtolower((string) request()->query('post_type', 'announcement'));

            $parentTerms = Term::where('taxonomy', $taxonomy)
                ->forPostType($currentPostType)
                ->orderBy('name', 'asc')
                ->get();
        }

        $this->setBreadcrumbTitle(__('Edit :taxLabel', ['taxLabel' => $taxonomyModel->label_singular]))
            ->addBreadcrumbItem($taxonomyModel->label, route('admin.terms.index', $taxonomy));

        $availablePostTypes = $this->contentService->getPostTypes();
        $postType = strtolower((string) request()->query('post_type', $availablePostTypes->has('announcement') ? 'announcement' : 'news'));

        if ($term->isProtectedForPostType($postType)) {
            return redirect()->route('admin.terms.index', [
                'taxonomy' => $taxonomy,
                'post_type' => $postType,
            ])->with('error', __('This category is protected and cannot be edited.'));
        }

        return $this->renderViewWithBreadcrumbs('backend.pages.terms.edit', [
            'taxonomy' => $taxonomy,
            'taxonomyModel' => $taxonomyModel,
            'term' => $term,
            'parentTerms' => $parentTerms,
            'postType' => $postType,
            'availablePostTypes' => $availablePostTypes,
            'postTypeModel' => $this->contentService->getPostType($postType),
        ]);
    }

    /**
     * Delete multiple terms at once
     */
    public function bulkDelete(Request $request, string $taxonomy)
    {
        $this->authorize('bulkDelete', Term::class);

        // Get taxonomy using service
        $taxonomyModel = $this->termService->getTaxonomy($taxonomy);

        if (! $taxonomyModel) {
            return redirect()->route('admin.posts.index')
                ->with('error', __('Taxonomy not found'));
        }

        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('admin.terms.index', $taxonomy)
                ->with('error', __('No terms selected for deletion'));
        }

        // Get taxonomy label for messages
        $taxLabel = $this->termService->getTaxonomyLabel($taxonomy, true);
        $deletedCount = 0;
        $errorMessages = [];

        $postTypeContext = strtolower((string) $request->input('context_post_type', $request->query('post_type')));

        foreach ($ids as $id) {
            // Get term using service
            $term = $this->termService->getTermById((int) $id, $taxonomy);

            if (! $term) {
                continue;
            }

            if ($term->isProtectedForPostType($postTypeContext)) {
                $errorMessages[] = __('":name" is protected and cannot be deleted.', ['name' => $term->name]);
                continue;
            }

            // Check if term can be deleted
            $errors = $this->termService->canDeleteTerm($term);

            if (! empty($errors)) {
                if (in_array('has_posts', $errors)) {
                    $errorMessages[] = __('":name" cannot be deleted as it is associated with posts', ['name' => $term->name]);
                }

                if (in_array('has_children', $errors)) {
                    $errorMessages[] = __('":name" cannot be deleted as it has child items', ['name' => $term->name]);
                }

                continue;
            }

            // Delete term using service
            $this->termService->deleteTerm($term);
            $deletedCount++;
        }

        if ($deletedCount > 0) {
            session()->flash('success', __(':count :taxLabel deleted successfully', [
                'count' => $deletedCount,
                'taxLabel' => strtolower($taxonomyModel->label),
            ]));
        }

        if (! empty($errorMessages)) {
            session()->flash('error', implode('<br>', $errorMessages));
        } elseif ($deletedCount === 0) {
            session()->flash('error', __('No :taxLabel were deleted', ['taxLabel' => strtolower($taxonomyModel->label)]));
        }

        $postType = $postTypeContext ?: 'announcement';

        return redirect()->route('admin.terms.index', [
            'taxonomy' => $taxonomy,
            'post_type' => $postType,
        ]);
    }
}
