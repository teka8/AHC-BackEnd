<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MediaFolder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class MediaFolderService
{
    public function __construct(private readonly MediaLibraryService $mediaLibraryService)
    {
    }

    public function getTree(): Collection
    {
        $folders = MediaFolder::query()
            ->withCount('media')
            ->orderBy('parent_id')
            ->orderBy('order_column')
            ->orderBy('name')
            ->get();

        return $this->buildTree($folders);
    }

    protected function buildTree(Collection $folders, ?int $parentId = null): Collection
    {
        return $folders
            ->where('parent_id', $parentId)
            ->map(function (MediaFolder $folder) use ($folders) {
                $folder->setRelation('children', $this->buildTree($folders, $folder->getKey()));

                return $folder;
            })
            ->values();
    }

    public function create(array $attributes): MediaFolder
    {
        $data = Arr::only($attributes, [
            'name',
            'description',
            'parent_id',
            'slug',
            'order_column',
        ]);

        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        if (! empty($data['slug'])) {
            $data['slug'] = MediaFolder::generateUniqueSlug($data['slug']);
        }

        return MediaFolder::create($data);
    }

    public function update(MediaFolder $folder, array $attributes): MediaFolder
    {
        $data = Arr::only($attributes, [
            'name',
            'description',
            'parent_id',
            'slug',
            'order_column',
        ]);

        $data['updated_by'] = auth()->id();

        if (! empty($data['slug'])) {
            $data['slug'] = MediaFolder::generateUniqueSlug($data['slug'], $folder->getKey());
        }

        $folder->fill($data);
        $folder->save();

        return $folder;
    }

    public function delete(MediaFolder $folder): bool
    {
        return (bool) $folder->delete();
    }

    public function getFolderWithMedia(int $folderId, array $filters = [], int $perPage = 24): array
    {
        /** @var MediaFolder $folder */
        $folder = MediaFolder::with('parent')->findOrFail($folderId);

        $mediaResult = $this->mediaLibraryService->getMediaList(
            $filters['search'] ?? null,
            $filters['type'] ?? null,
            $filters['sort'] ?? 'created_at',
            $filters['direction'] ?? 'desc',
            $perPage,
            $folderId,
            'folder_media'
        );

        return [
            'folder' => $folder,
            'media' => $mediaResult['media'],
            'stats' => $mediaResult['stats'],
        ];
    }

    public function uploadToFolder(MediaFolder $folder, array $files, array $captions = []): array
    {
        return $this->mediaLibraryService->uploadMedia($files, $folder, 'folder_media', $captions);
    }
}
