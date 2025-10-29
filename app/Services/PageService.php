<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Page;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PageService
{
    /**
     * Get paginated pages with filters.
     */
    public function getPages(array $filters = []): LengthAwarePaginator
    {
        $query = Page::query()->with(['createdBy']);

        // Apply custom filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['section'])) {
            $query->where('section', $filters['section']);
        }

        if (!empty($filters['is_custom_section'])) {
            $query->where('is_custom_section', $filters['is_custom_section']);
        }

        if (!empty($filters['show_in_nav'])) {
            $query->where('show_in_nav', $filters['show_in_nav']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('content', 'like', "%{$filters['search']}%")
                  ->orWhere('slug', 'like', "%{$filters['search']}%")
                  ->orWhere('section', 'like', "%{$filters['search']}%");
            });
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    /**
     * Get single page by ID.
     */
    public function getPageById(?int $id): ?Page
    {
        if (empty($id)) {
            return null;
        }

        return Page::with(['createdBy'])->findOrFail($id);
    }

    /**
     * Get page by slug.
     */
    public function getPageBySlug(string $slug): ?Page
    {
        return Page::where('slug', $slug)
            ->where('status', 'published')
            ->first();
    }

    /**
     * Create new page.
     */
    public function createPage(array $data): Page
    {
        // Use a transaction to ensure atomicity
        return DB::transaction(function () use ($data) {
            // Handle custom section logic
            $section = $data['section'] ?? null;
            $isCustomSection = $data['is_custom_section'] ?? false;
            
            if ($isCustomSection && !empty($data['custom_section'])) {
                $section = $data['custom_section'];
            }

            // Create the page safely using null coalescing for optional fields
            $page = Page::create([
                'title' => $data['title'], // required
                'slug' => $data['slug'], // required
                'content' => $data['content'] ?? null,
                'section' => $section,
                'is_custom_section' => $isCustomSection,
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
                'show_in_nav' => $data['show_in_nav'] ?? true,
                'show_in_footer' => $data['show_in_footer'] ?? false,
                'status' => $data['status'] ?? 'draft',
                'created_by' => $data['created_by'] ?? auth()->id(),
                'updated_by' => $data['updated_by'] ?? auth()->id(),
            ]);

            return $page;
        });
    }

    /**
     * Update existing page.
     */
    public function updatePage(Page $page, array $data): Page
    {
        // Handle custom section logic
        $section = $data['section'] ?? $page->section;
        $isCustomSection = $data['is_custom_section'] ?? $page->is_custom_section;
        
        if ($isCustomSection && !empty($data['custom_section'])) {
            $section = $data['custom_section'];
        }

        $page->update([
            'title' => $data['title'] ?? $page->title,
            'slug' => $data['slug'] ?? $page->slug,
            'content' => $data['content'] ?? $page->content,
            'section' => $section,
            'is_custom_section' => $isCustomSection,
            'meta_title' => $data['meta_title'] ?? $page->meta_title,
            'meta_description' => $data['meta_description'] ?? $page->meta_description,
            'show_in_nav' => $data['show_in_nav'] ?? $page->show_in_nav,
            'show_in_footer' => $data['show_in_footer'] ?? $page->show_in_footer,
            'status' => $data['status'] ?? $page->status,
            'updated_by' => $data['updated_by'] ?? auth()->id(),
        ]);

        return $page;
    }

    /**
     * Delete a page.
     */
    public function deletePage(Page $page): void
    {
        $page->delete();
    }

    /**
     * Bulk delete multiple pages.
     */
    public function bulkDeletePages(array $ids): int
    {
        if (empty($ids)) {
            return 0;
        }

        $pages = Page::whereIn('id', $ids)->get();
        $deletedCount = 0;

        foreach ($pages as $page) {
            $this->deletePage($page);
            $deletedCount++;
        }

        return $deletedCount;
    }

    /**
     * Get page permalink (frontend URL).
     */
    public function getPagePermalink(Page|int|null $page): ?string
    {
        if (is_numeric($page)) {
            $page = $this->getPageById($page);
        }

        if (!$page) {
            return null;
        }

        return route('admin.pages.show', ['slug' => $page->slug]);
    }

    /**
     * Get navigation pages.
     */
    public function getNavigationPages(): \Illuminate\Database\Eloquent\Collection
    {
        return Page::where('status', 'published')
            ->where('show_in_nav', true)
            ->orderBy('title')
            ->get();
    }

    /**
     * Get footer pages.
     */
    public function getFooterPages(): \Illuminate\Database\Eloquent\Collection
    {
        return Page::where('status', 'published')
            ->where('show_in_footer', true)
            ->orderBy('title')
            ->get();
    }

    /**
     * Get pages by section.
     */
    public function getPagesBySection(string $section): \Illuminate\Database\Eloquent\Collection
    {
        return Page::where('section', $section)
            ->where('status', 'published')
            ->orderBy('title')
            ->get();
    }

    /**
     * Update page status.
     */
    public function updatePageStatus(Page $page, string $status, ?int $updatedBy = null): Page
    {
        $page->update([
            'status' => $status,
            'updated_by' => $updatedBy ?? auth()->id(),
        ]);

        return $page;
    }

    /**
     * Generate unique slug from title.
     */
    public function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = \Illuminate\Support\Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (Page::where('slug', $slug)
            ->when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get page statistics.
     */
    public function getPageStats(): array
    {
        return [
            'total' => Page::count(),
            'published' => Page::where('status', 'published')->count(),
            'draft' => Page::where('status', 'draft')->count(),
            'archived' => Page::where('status', 'archived')->count(),
            'in_navigation' => Page::where('show_in_nav', true)->count(),
            'in_footer' => Page::where('show_in_footer', true)->count(),
        ];
    }
}