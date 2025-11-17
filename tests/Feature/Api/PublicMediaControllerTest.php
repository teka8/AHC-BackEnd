<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Media;
use App\Models\MediaFolder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicMediaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_it_returns_root_folders_with_preview_media(): void
    {
        $folder = MediaFolder::factory()->create(['name' => 'Root Folder']);

        $this->makeMedia($folder, ['file_name' => 'preview.jpg', 'mime_type' => 'image/jpeg']);

        $response = $this->getJson('/api/v1/public/media');

        $response->assertOk()
            ->assertJsonPath('folder', null)
            ->assertJsonPath('folders.0.id', $folder->id)
            ->assertJsonPath('folders.0.media_count', 1)
            ->assertJsonPath('folders.0.preview_media.0.type', 'image');
    }

    public function test_it_returns_folder_details_with_children_and_media(): void
    {
        $parent = MediaFolder::factory()->create(['name' => 'Parent']);
        $child = MediaFolder::factory()->create(['name' => 'Child', 'parent_id' => $parent->id]);

        $mediaImage = $this->makeMedia($parent, ['file_name' => 'image.jpg', 'mime_type' => 'image/jpeg']);
        $mediaVideo = $this->makeMedia($parent, ['file_name' => 'clip.mp4', 'mime_type' => 'video/mp4']);

        $response = $this->getJson('/api/v1/public/media?folder=' . $parent->id);

        $response->assertOk()
            ->assertJsonPath('folder.id', $parent->id)
            ->assertJsonPath('folders.0.id', $child->id)
            ->assertJsonPath('media.meta.total', 2)
            ->assertJsonPath('folder.breadcrumbs.0.id', $parent->id)
            ->assertJsonFragment(['id' => $mediaImage->id, 'type' => 'image'])
            ->assertJsonFragment(['id' => $mediaVideo->id, 'type' => 'video']);
    }

    private function makeMedia(MediaFolder $folder, array $overrides = []): Media
    {
        $attributes = array_merge([
            'model_type' => '',
            'model_id' => 0,
            'uuid' => (string) Str::uuid(),
            'collection_name' => 'folder_media',
            'name' => Str::title(pathinfo($overrides['file_name'] ?? 'file.jpg', PATHINFO_FILENAME)),
            'file_name' => $overrides['file_name'] ?? 'file.jpg',
            'mime_type' => $overrides['mime_type'] ?? 'image/jpeg',
            'disk' => 'public',
            'conversions_disk' => 'public',
            'size' => 1024,
            'manipulations' => '[]',
            'custom_properties' => ['caption' => 'Sample caption'],
            'generated_conversions' => '[]',
            'responsive_images' => '[]',
            'order_column' => null,
            'folder_id' => $folder->id,
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides);

        $media = Media::query()->create($attributes);

        Storage::disk('public')->put('media/' . $media->file_name, 'test');

        return $media;
    }
}
