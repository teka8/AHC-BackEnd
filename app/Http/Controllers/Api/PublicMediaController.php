<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\MediaFolder;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicMediaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $folderParam = $request->query('folder');
        $type = $request->query('type');
        $perPage = (int) $request->query('per_page', 24);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 24;

        if ($folderParam) {
            $folder = MediaFolder::query()
                ->with('parent')
                ->where(function ($query) use ($folderParam) {
                    if (is_numeric($folderParam)) {
                        $query->whereKey((int) $folderParam);
                    } else {
                        $query->where('slug', $folderParam)
                            ->orWhere('uuid', $folderParam);
                    }
                })
                ->firstOrFail();

            $mediaQuery = Media::query()
                ->where('folder_id', $folder->getKey())
                ->where('collection_name', 'folder_media')
                ->orderByDesc('created_at');

            if ($type) {
                $mediaQuery->where('mime_type', 'like', $this->mimePattern($type));
            }

            $paginator = $mediaQuery->paginate($perPage);

            $mediaItems = $paginator->getCollection()
                ->map(fn (Media $media) => $this->transformMedia($media))
                ->values();

            $childFolders = MediaFolder::query()
                ->where('parent_id', $folder->getKey())
                ->withCount(['media', 'children'])
                ->with(['media' => fn ($query) => $query->take(4)])
                ->orderBy('order_column')
                ->get();

            $currentFolder = $this->transformFolder($folder);
            $currentFolder['breadcrumbs'] = $this->buildBreadcrumbs($folder);

            return response()->json([
                'folder' => $currentFolder,
                'folders' => $childFolders->map(fn (MediaFolder $f) => $this->transformFolder($f))->values(),
                'media' => [
                    'data' => $mediaItems,
                    'meta' => [
                        'current_page' => $paginator->currentPage(),
                        'per_page' => $paginator->perPage(),
                        'total' => $paginator->total(),
                        'last_page' => $paginator->lastPage(),
                    ],
                ],
            ]);
        }

        $folders = MediaFolder::query()
            ->whereNull('parent_id')
            ->withCount(['media', 'children'])
            ->with(['media' => fn ($query) => $query->take(4)])
            ->orderBy('order_column')
            ->get();

        return response()->json([
            'folder' => null,
            'folders' => $folders->map(fn (MediaFolder $folder) => $this->transformFolder($folder))->values(),
            'media' => null,
        ]);
    }

    private function transformFolder(MediaFolder $folder): array
    {
        $previewSource = $folder->relationLoaded('media') ? $folder->media : $folder->media()->limit(4)->get();

        return [
            'id' => $folder->getKey(),
            'uuid' => $folder->uuid,
            'name' => $folder->name,
            'slug' => $folder->slug,
            'description' => $folder->description,
            'parent_id' => $folder->parent_id,
            'media_count' => $folder->media_count ?? $folder->media()->count(),
            'children_count' => $folder->children_count ?? $folder->children()->count(),
            'preview_media' => $this->transformPreviewMedia($previewSource),
        ];
    }

    private function transformPreviewMedia(Collection $media): array
    {
        return $media->map(fn (Media $mediaItem) => [
            'id' => $mediaItem->getKey(),
            'type' => $this->detectType($mediaItem->mime_type),
            'url' => $this->resolveUrl($mediaItem),
            'thumb_url' => $this->resolveThumbUrl($mediaItem),
        ])->values()->all();
    }

    private function transformMedia(Media $media): array
    {
        return [
            'id' => $media->getKey(),
            'name' => $media->name,
            'file_name' => $media->file_name,
            'mime_type' => $media->mime_type,
            'type' => $this->detectType($media->mime_type),
            'size' => $media->size,
            'caption' => $media->getCustomProperty('caption'),
            'url' => $this->resolveUrl($media),
            'thumb_url' => $this->resolveThumbUrl($media),
            'created_at' => optional($media->created_at)->toISOString(),
        ];
    }

    private function detectType(?string $mime): string
    {
        if (blank($mime)) {
            return 'file';
        }

        return match (true) {
            Str::startsWith($mime, 'image/') => 'image',
            Str::startsWith($mime, 'video/') => 'video',
            Str::startsWith($mime, 'audio/') => 'audio',
            default => 'file',
        };
    }

    private function resolveUrl(Media $media): string
    {
        $fileName = ltrim((string) $media->file_name, '/');

        if (blank($fileName)) {
            return $this->normalizeUrl('storage/media');
        }

        if (blank($media->model_type) || (int) $media->model_id === 0) {
            return $this->normalizeUrl('storage/media/' . $fileName);
        }

        try {
            $candidate = $media->getUrl();
            if ($this->urlLooksUsable($candidate)) {
                return $this->normalizeUrl($candidate);
            }
        } catch (\Throwable $e) {
            // Ignore and continue with disk-based fallbacks.
        }

        $disk = $this->resolveDisk($media->disk ?? config('filesystems.default'));

        if ($disk) {
            foreach ($this->preferredDiskPaths($media, $fileName) as $path) {
                try {
                    if ($disk->exists($path)) {
                        $candidate = $disk->url($path);

                        if ($this->urlLooksUsable($candidate)) {
                            return $this->normalizeUrl($candidate);
                        }
                    }
                } catch (\Throwable $e) {
                    // Ignore and move on to the next path option.
                }
            }
        }

        foreach ($this->buildFallbackRelativePaths($media, $fileName) as $relative) {
            $candidate = asset($relative);

            if ($this->urlLooksUsable($candidate)) {
                return $this->normalizeUrl($candidate);
            }
        }

        return $this->normalizeUrl('storage/media/' . $fileName);
    }

    private function resolveThumbUrl(Media $media): string
    {
        if ($media->hasGeneratedConversion('thumb')) {
            try {
                $thumbUrl = $media->getUrl('thumb');

                if ($this->urlLooksUsable($thumbUrl)) {
                    return $this->normalizeUrl($thumbUrl);
                }
            } catch (\Throwable $e) {
                // Fall through to main URL.
            }
        }

        return $this->resolveUrl($media);
    }

    private function urlLooksUsable(?string $url): bool
    {
        return is_string($url) && $url !== '' && $url !== '/';
    }

    private function buildBreadcrumbs(MediaFolder $folder): array
    {
        $breadcrumbs = [];
        $current = $folder;

        while ($current) {
            array_unshift($breadcrumbs, [
                'id' => $current->getKey(),
                'name' => $current->name,
                'slug' => $current->slug,
            ]);

            $current = $current->relationLoaded('parent') ? $current->parent : $current->parent()->first();
        }

        return $breadcrumbs;
    }

    private function mimePattern(string $type): string
    {
        return match ($type) {
            'image', 'video', 'audio' => $type . '/%',
            default => '%',
        };
    }

    private function preferredDiskPaths(Media $media, string $fileName): array
    {
        $paths = [];

        if (! empty($fileName)) {
            $paths[] = $fileName;
            $paths[] = 'media/' . $fileName;
            $paths[] = 'folder_media/' . $fileName;
            $paths[] = 'uploads/' . $fileName;

            if (! empty($media->collection_name)) {
                $paths[] = trim($media->collection_name . '/' . $fileName, '/');
                $paths[] = 'media/' . trim($media->collection_name . '/' . $fileName, '/');
            }
        }

        if (method_exists($media, 'getPathRelativeToRoot')) {
            try {
                $relative = ltrim((string) $media->getPathRelativeToRoot(), '/');
                if (! empty($relative)) {
                    $paths[] = $relative;
                }
            } catch (\Throwable $e) {
                // Ignore failures when resolving the relative path.
            }
        }

        return array_values(array_unique(array_filter($paths)));
    }

    private function buildFallbackRelativePaths(Media $media, string $fileName): array
    {
        $relative = [];

        if (! empty($fileName)) {
            $relative[] = 'storage/' . $fileName;
            $relative[] = 'storage/media/' . $fileName;
            $relative[] = 'storage/folder_media/' . $fileName;
            $relative[] = 'storage/uploads/' . $fileName;

            if (! empty($media->collection_name)) {
                $relative[] = 'storage/' . trim($media->collection_name . '/' . $fileName, '/');
                $relative[] = 'storage/media/' . trim($media->collection_name . '/' . $fileName, '/');
            }
        }

        return array_values(array_unique($relative));
    }

    private function resolveDisk(?string $diskName): ?Filesystem
    {
        if (blank($diskName)) {
            return null;
        }

        try {
            return Storage::disk($diskName);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function normalizeUrl(string $url): string
    {
        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        $base = config('app.asset_url') ?? config('app.url');

        if ($base) {
            return rtrim($base, '/') . '/' . ltrim($url, '/');
        }

        return url($url);
    }

}
