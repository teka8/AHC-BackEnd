<?php

namespace App\Providers;

use App\Enums\Hooks\ContentActionHook;
use App\Services\Content\ContentService;
use App\Support\Facades\Hook;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class ContentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ContentService::class, function ($app) {
            return new ContentService();
        });
    }

    public function boot(): void
    {
        // Skip registering taxonomies if tables don't exist yet.
        try {
            if (! Schema::hasTable('taxonomies')) {
                return;
            }

            // Register default post types.
            $this->registerDefaultPostTypes();

            // Register default taxonomies.
            $this->registerDefaultTaxonomies();
        } catch (QueryException $e) {
            // Handle database connection issues or other query-related errors
            // Just exit gracefully for now.
            return;
        }
    }

    protected function tablesExist(array $tables): bool
    {
        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }

    protected function registerDefaultPostTypes(): void
    {
        $contentService = app(ContentService::class);

        // Reset cached definitions so defaults stay in sync on every boot.
        $contentService->clearPostTypesCache();

        // Register post type.
        $contentService->registerPostType([
            'name' => 'news',
            'label' => 'News',
            'label_singular' => 'News',
            'description' => 'Latest stories and updates',
            'taxonomies' => ['category', 'tag'],
            'show_in_menu' => true,
        ]);

        $contentService->registerPostType([
            'name' => 'announcement',
            'label' => 'Announcements',
            'label_singular' => 'Announcement',
            'description' => 'Official announcements shared with the public',
            'taxonomies' => ['category', 'tag'],
            'show_in_menu' => true,
        ]);

        // Register page type.
        // $contentService->registerPostType([
        //     'name' => 'page',
        //     'label' => 'Pages',
        //     'label_singular' => 'Page',
        //     'description' => 'Default post type for static pages',
        //     'has_archive' => false,
        //     'hierarchical' => true,
        //     'supports_excerpt' => true,
        //     'taxonomies' => [],
        // ]);

        // Allow other plugins/modules to register post types.
        Hook::doAction(ContentActionHook::REGISTER_POST_TYPES, $contentService);
    }

    protected function registerDefaultTaxonomies(): void
    {
        $contentService = app(ContentService::class);

        // Register category taxonomy for posts.
        $contentService->registerTaxonomy([
            'name' => 'category',
            'label' => 'Categories',
            'label_singular' => 'Category',
            'description' => 'Default taxonomy for categorizing posts',
            'hierarchical' => true,
            'show_featured_image' => true,
        ], 'post');

        // Register tag taxonomy for posts.
        $contentService->registerTaxonomy([
            'name' => 'tag',
            'label' => 'Tags',
            'label_singular' => 'Tag',
            'description' => 'Default taxonomy for tagging posts',
            'hierarchical' => false,
            'show_featured_image' => true,
        ], 'post');

        // Allow other plugins/modules to register taxonomies
        Hook::doAction(ContentActionHook::REGISTER_TAXONOMIES, $contentService);
    }

    protected function getPostTypeIcon(string $postType): string
    {
        return match ($postType) {
            'post' => 'lucide:file-text',
            'page' => 'lucide:file',
            default => 'lucide:files'
        };
    }

    protected function getTaxonomyIcon(string $taxonomy): string
    {
        return match ($taxonomy) {
            'category' => 'lucide:folder',
            'tag' => 'lucide:tags',
            default => 'lucide:bookmark'
        };
    }
}
