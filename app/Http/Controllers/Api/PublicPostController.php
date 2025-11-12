<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PublicPostController extends Controller
{
    /**
     * Public list of published posts (post_type = post), no auth required.
     */
    public function index(Request $request)
    {
        $perPage = (int) ($request->input('per_page') ?? config('settings.default_pagination', 10));
        $search = (string) ($request->input('search') ?? '');
        $pillarParam = $request->input('pillar');

        $query = Post::query()
            ->where('post_type', 'news')
            ->where('status', PostStatus::PUBLISHED->value);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if (! empty($pillarParam)) {
            $pillars = is_array($pillarParam)
                ? $pillarParam
                : (preg_split('/[,|]/', (string) $pillarParam) ?: []);

            $pillars = collect($pillars)
                ->map(fn ($value) => is_string($value) ? strtolower(trim($value)) : null)
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (! empty($pillars)) {
                $query->where(function ($q) use ($pillars) {
                    foreach ($pillars as $pillar) {
                        $q->orWhere(function ($inner) use ($pillar) {
                            $normalized = strtolower($pillar);
                            $encodedNeedle = '%"' . $normalized . '"%';
                            $wildcardNeedle = '%' . $normalized . '%';

                            $inner->orWhereRaw('LOWER(COALESCE(pillars, \'\')) = ?', [$normalized])
                                  ->orWhereRaw('LOWER(COALESCE(pillars, \'\')) like ?', [$encodedNeedle])
                                  ->orWhereRaw('LOWER(COALESCE(pillars, \'\')) like ?', [$wildcardNeedle]);
                        });
                    }
                });
            }
        }

        // Order by created_at to avoid DB errors if published_at column is absent or null
        $posts = $query->orderByDesc('created_at')->paginate($perPage);

        return PostResource::collection($posts)->additional([
            'meta' => [
                'current_page' => $posts->currentPage(),
                'from' => $posts->firstItem(),
                'last_page' => $posts->lastPage(),
                'path' => $posts->path(),
                'per_page' => $posts->perPage(),
                'to' => $posts->lastItem(),
                'total' => $posts->total(),
            ],
            'links' => [
                'first' => $posts->url(1),
                'last' => $posts->url($posts->lastPage()),
                'prev' => $posts->previousPageUrl(),
                'next' => $posts->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Public single post by ID (published only).
     */
    public function show(int $id)
    {
        $post = Post::where('post_type', 'news')
            ->where('status', 'Published')
            ->findOrFail($id);

        return new PostResource($post);
    }
}
