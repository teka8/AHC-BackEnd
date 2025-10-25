<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PageStatus;
use App\Models\Page;

class PageService
{
    /**
     * Get pages with filters
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPages(array $filters = [])
    {
        // Set default post type if not provided.
        if (!isset($filters['post_type'])) {
            $filters['post_type'] = 'page';
        }

        // Create base query with post type filter.
        $query = Page::where('post_type', $filters['post_type'])
            ->with(['user', 'terms']);

        // Handle category filter separately.
        if (isset($filters['category']) && !empty($filters['category'])) {
            $query->filterByCategory($filters['category']);
            unset($filters['category']); // Remove to prevent double filtering
        }

        // Handle tag filter separately.
        if (isset($filters['tag']) && !empty($filters['tag'])) {
            $query->filterByTag($filters['tag']);
            unset($filters['tag']); // Remove to prevent double filtering
        }

        $query = $query->applyFilters($filters);

        return $query->paginateData([
            'per_page' => config('settings.default_pagination') ?? 10,
        ]);
    }

    /**
     * Get a post by ID.
     */
    public function getPageById(?int $id, ?string $postType = null): ?Page
    {
        if (empty($id)) {
            return null;
        }

        $query = Page::query();

        if ($postType) {
            $query->where('post_type', $postType)
                ->with(['user', 'terms']);
        }

        return $query->findOrFail($id);
    }

    /**
     * Get paginated pages with filters
     */
    public function getPaginatedPages(array $filters = [], int $perPage = 10)
    {
        // Set default post type if not provided.
        if (!isset($filters['post_type'])) {
            $filters['post_type'] = 'page';
        }

        // Create base query with post type filter.
        $query = Page::where('post_type', $filters['post_type'])
            ->with(['author', 'terms']);

        // Handle category filter separately.
        if (isset($filters['category']) && !empty($filters['category'])) {
            $query->filterByCategory($filters['category']);
            unset($filters['category']);
        }

        // Handle tag filter separately.
        if (isset($filters['tag']) && !empty($filters['tag'])) {
            $query->filterByTag($filters['tag']);
            unset($filters['tag']);
        }

        $query = $query->applyFilters($filters);

        return $query->paginate($perPage);
    }

    /**
     * Create a new page
     */
    public function createPage(array $data): Page
    {
        $page = Page::create([
            'title' => $data['title'],
            'slug' => $data['slug'] ?? str()->slug($data['title']),
            'content' => $data['content'] ?? '',
            'excerpt' => $data['excerpt'] ?? '',
            'post_type' => $data['post_type'] ?? 'page',
            'status' => $data['status'] ?? PageStatus::CREATED->value,
            'published_at' => $data['published_at'] ?? null,
            'author_id' => $data['author_id'],
        ]);


        // Sync terms if provided
        if (isset($data['terms']) && !empty($data['terms'])) {
            $page->terms()->sync($data['terms']);
        }


        return $page->load(['author', 'terms']);
    }

    /**
     * Update an existing page
     */
    public function updatePage(Page $page, array $data): Page
    {
        $updateData = [
            'title' => $data['title'] ?? $page->title,
            'slug' => $data['slug'] ?? $page->slug,
            'content' => $data['content'] ?? $page->content,
            'excerpt' => $data['excerpt'] ?? $page->excerpt,
            'status' => $data['status'] ?? $page->status,
            'published_at' => $data['published_at'] ?? $page->published_at,
        ];

        $page->update($updateData);


        // Sync terms if provided
        if (isset($data['terms'])) {
            $page->terms()->sync($data['terms']);
        }

        return $page->load(['author', 'terms']);
    }

    /**
     * Delete multiple posts
     */
    public function bulkDeletePages(array $ids, string $postType = 'page'): int
    {
        if (empty($ids)) {
            return 0;
        }

        $pages = Page::where('post_type', $postType)
            ->whereIn('id', $ids)
            ->get();

        $deletedCount = 0;
        foreach ($pages as $page) {
            $page->delete();
            $deletedCount++;
        }

        return $deletedCount;
    }

    public function getPagePermalink(Page|int|null $page): ?string
    {
        if (is_numeric($page)) {
            $page = $this->getPageById($page);
        }

        if (!$page) {
            return null;
        }

        return route('page.show', ['post_type' => $page->post_type, 'slug' => $page->slug]);
    }

    public function getPageDate(Page|int|null $page, string $format = 'M d, Y'): ?string
    {
        if (is_numeric($page)) {
            $page = $this->getPageById($page);
        }

        if (!$page) {
            return null;
        }

        return $page->published_at ?
            $page->published_at->format($format) : $page->created_at->format($format);
    }

    public function getPageTerms(Page|int|null $page, string $taxonomy)
    {
        if (is_numeric($page)) {
            $page = $this->getPageById($page);
        }

        if (!$page) {
            return collect();
        }

        if ($taxonomy) {
            return $page->terms()->where('taxonomy', $taxonomy)->get();
        }

        return $page->terms;
    }
}