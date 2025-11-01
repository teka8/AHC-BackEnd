<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PublicMediaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Media::query();

        // Optional filters
        if ($type = $request->query('type')) {
            if ($type === 'image') {
                $query->where('mime_type', 'like', 'image/%');
            } elseif ($type === 'video') {
                $query->where('mime_type', 'like', 'video/%');
            } elseif ($type === 'audio') {
                $query->where('mime_type', 'like', 'audio/%');
            }
        }
        if ($collection = $request->query('collection')) {
            $query->where('collection_name', $collection);
        }

        $query->orderByDesc('created_at');

        $perPage = (int) $request->query('per_page', 24);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 24;

        $paginator = $query->paginate($perPage);

        $items = $paginator->getCollection()->map(function (Media $m) {
            // getUrl() provides a publicly accessible URL when using public disk
            return [
                'id' => $m->id,
                'name' => $m->name,
                'file_name' => $m->file_name,
                'mime_type' => $m->mime_type,
                'size' => $m->size,
                'collection' => $m->collection_name,
                'url' => $m->getUrl(),
                'created_at' => optional($m->created_at)->toISOString(),
            ];
        })->values();

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }
}
