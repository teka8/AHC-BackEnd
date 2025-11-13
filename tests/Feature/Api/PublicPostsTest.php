<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPostsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_news_posts_by_default(): void
    {
        $news = Post::factory()->create([
            'post_type' => 'news',
            'status' => PostStatus::PUBLISHED->value,
            'published_at' => now(),
        ]);

        Post::factory()->create([
            'post_type' => 'announcement',
            'status' => PostStatus::PUBLISHED->value,
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/public/posts');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $this->assertSame('news', strtolower((string) ($response->json('data.0.post_type') ?? '')));
        $this->assertSame($news->id, $response->json('data.0.id'));
    }

    public function test_it_can_filter_announcements(): void
    {
        Post::factory()->create([
            'post_type' => 'news',
            'status' => PostStatus::PUBLISHED->value,
            'published_at' => now(),
        ]);

        $announcement = Post::factory()->create([
            'post_type' => 'announcement',
            'status' => PostStatus::PUBLISHED->value,
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/public/posts?type=announcement');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $this->assertSame('announcement', strtolower((string) ($response->json('data.0.post_type') ?? '')));
        $this->assertSame($announcement->id, $response->json('data.0.id'));
    }

    public function test_it_can_show_a_single_announcement(): void
    {
        $announcement = Post::factory()->create([
            'post_type' => 'announcement',
            'status' => PostStatus::PUBLISHED->value,
            'published_at' => now(),
        ]);

        $response = $this->getJson("/api/v1/public/posts/{$announcement->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $announcement->id);
        $this->assertSame('announcement', strtolower((string) ($response->json('data.post_type') ?? '')));
    }
}
