<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample news posts if none exist
        if (Post::where('post_type', 'news')->count() === 0) {
            Post::create([
                'title' => 'Welcome to News Section',
                'slug' => 'welcome-to-news-section',
                'content' => '<p>This is a sample news article to demonstrate the news functionality.</p>',
                'status' => 'created',
                'post_type' => 'news',
                'user_id' => 1,
                'published_at' => now(),
            ]);
        }
    }
}