<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsPostTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Register news post type in the system
        $newsPostType = [
            'name' => 'news',
            'label' => 'News',
            'label_singular' => 'News Article',
            'description' => 'News articles and updates',
            'public' => true,
            'show_in_menu' => true,
            'supports_editor' => true,
            'supports_excerpt' => false,
            'supports_thumbnail' => true,
            'hierarchical' => false,
            'taxonomies' => ['category', 'tag'],
            'menu_icon' => 'lucide:newspaper',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Insert or update the post type configuration
        DB::table('post_types')->updateOrInsert(
            ['name' => 'news'],
            $newsPostType
        );
    }
}