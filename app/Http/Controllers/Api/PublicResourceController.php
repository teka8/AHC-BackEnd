<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EducationalResourcePublicResource;
use App\Http\Resources\DocumentPublicResource;
use App\Models\EducationalResource;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Others;
use App\Models\OthersCategory;
use Illuminate\Http\Request;

class PublicResourceController extends Controller
{
    /**
     * GET /api/v1/public/resources/educational
     * Query params: search, category, type, per_page=50, page=1
     */
    public function educational(Request $request)
    {
        $perPage = (int) ($request->input('per_page') ?? 50);
        $search = (string) ($request->input('search') ?? '');
        $category = (string) ($request->input('category') ?? '');
        $type = (string) ($request->input('type') ?? '');

        $query = EducationalResource::query()
            ->whereIn('access_level', ['public', 'Public'])
            ->whereIn('status', ['published', 'Published']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('creator', 'like', "%{$search}%");
            });
        }
        if ($category !== '') {
            $query->where('subject_area', $category);
        }
        if ($type !== '') {
            $query->where('resource_type', $type);
        }

        $query->orderByDesc('published_at')->orderByDesc('created_at');
        $resources = $query->paginate($perPage);
        return EducationalResourcePublicResource::collection($resources)->additional([
            'links' => [
                'first' => $resources->url(1),
                'last' => $resources->url($resources->lastPage()),
                'prev' => $resources->previousPageUrl(),
                'next' => $resources->nextPageUrl(),
            ],
        ]);
    }

    public function educationalShow(int $id)
    {
        $item = EducationalResource::whereIn('access_level', ['public', 'Public'])
            ->whereIn('status', ['published', 'Published'])
            ->findOrFail($id);
        return new EducationalResourcePublicResource($item);
    }

    /**
     * GET /api/v1/public/resources/others
     * Query params: search, category (subject_area), type (resource_type), per_page=50, page=1
     */
    public function others(Request $request)
    {
        $perPage = (int) ($request->input('per_page') ?? 50);
        $search = (string) ($request->input('search') ?? '');
        $category = (string) ($request->input('category') ?? '');
        $type = (string) ($request->input('type') ?? '');

        $query = Others::query()
            ->whereIn('access_level', ['public', 'Public'])
            ->whereIn('status', ['published', 'Published']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('creator', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($category !== '') {
            $query->where('subject_area', $category);
        }
        if ($type !== '') {
            $query->where('resource_type', $type);
        }

        $query->orderByDesc('published_at')->orderByDesc('created_at');
        $items = $query->paginate($perPage);
        return \App\Http\Resources\OthersPublicResource::collection($items)->additional([
            'links' => [
                'first' => $items->url(1),
                'last' => $items->url($items->lastPage()),
                'prev' => $items->previousPageUrl(),
                'next' => $items->nextPageUrl(),
            ],
        ]);
    }

    /**
     * GET /api/v1/public/resources/others/{id}
     */
    public function othersShow(int $id)
    {
        $item = Others::whereIn('access_level', ['public', 'Public'])
            ->whereIn('status', ['published', 'Published'])
            ->findOrFail($id);
        return new \App\Http\Resources\OthersPublicResource($item);
    }

    /**
     * GET /api/v1/public/resources/documents
     * Query params: search, category, type, per_page=50, page=1
     */
    public function documents(Request $request)
    {
        $perPage = (int) ($request->input('per_page') ?? 50);
        $search = (string) ($request->input('search') ?? '');
        $category = (string) ($request->input('category') ?? '');
        $type = (string) ($request->input('type') ?? '');

        $query = Document::query()
            ->whereIn('access_level', ['public', 'Public'])
            ->whereIn('status', ['published', 'Published']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('abstract', 'like', "%{$search}%");
            });
        }
        if ($category !== '') {
            $query->byCategory($category);
        }
        if ($type !== '') {
            $query->byType($type);
        }

        $query->orderByDesc('publication_date')->orderByDesc('created_at');
        $docs = $query->paginate($perPage);
        return DocumentPublicResource::collection($docs)->additional([
            'links' => [
                'first' => $docs->url(1),
                'last' => $docs->url($docs->lastPage()),
                'prev' => $docs->previousPageUrl(),
                'next' => $docs->nextPageUrl(),
            ],
        ]);
    }

    /**
     * GET /api/v1/public/resources/documents/{id}
     */
    public function documentsShow(int $id)
    {
        $doc = Document::whereIn('access_level', ['public', 'Public'])
            ->whereIn('status', ['published', 'Published'])
            ->findOrFail($id);
        return new DocumentPublicResource($doc);
    }

    /**
     * GET /api/v1/public/resources/documents/categories
     * Returns a list of active document categories (id, name, slug) if available,
     * otherwise falls back to distinct categories from documents table.
     */
    public function documentCategories()
    {
        try {
            // Preferred: use DocumentCategory model if present, but only those with at least one resource
            if (class_exists(\App\Models\DocumentCategory::class)) {
                $cats = DocumentCategory::active()
                    ->whereHas('documents', function ($q) {
                        $q->whereIn('access_level', ['public', 'Public'])
                          ->whereIn('status', ['published', 'Published']);
                    })
                    ->ordered()
                    ->get(['id', 'name', 'slug']);
                return response()->json(['data' => $cats]);
            }
        } catch (\Throwable $t) {
            // fall through to distinct
        }
        // Fallback: distinct categories from documents
        $cats = Document::query()
            ->public()
            ->published()
            ->whereNotNull('category')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->get()
            ->map(fn($row) => ['id' => null, 'name' => $row->category, 'slug' => \Str::slug($row->category)]);
        return response()->json(['data' => $cats]);
    }

    /**
     * GET /api/v1/public/resources/educational/categories
     * Returns distinct subject_area values as categories for educational resources.
     */
    public function educationalCategories()
    {
        $cats = EducationalResource::query()
            ->public()
            ->published()
            ->whereNotNull('subject_area')
            ->select('subject_area')
            ->distinct()
            ->orderBy('subject_area')
            ->get()
            ->map(fn($row) => ['id' => null, 'name' => $row->subject_area, 'slug' => \Str::slug($row->subject_area)]);
        return response()->json(['data' => $cats]);
    }

    /**
     * GET /api/v1/public/resources/others/categories
     * Returns Others categories (subject_area) via model if exists, else distinct.
     */
    public function othersCategories()
    {
        try {
            if (class_exists(\App\Models\OthersCategory::class)) {
                $cats = OthersCategory::active()->ordered()->get(['id', 'name', 'slug']);
                return response()->json(['data' => $cats]);
            }
        } catch (\Throwable $t) {
            // fallback below
        }
        $cats = Others::query()
            ->whereIn('access_level', ['public', 'Public'])
            ->whereIn('status', ['published', 'Published'])
            ->whereNotNull('subject_area')
            ->select('subject_area')
            ->distinct()
            ->orderBy('subject_area')
            ->get()
            ->map(fn($row) => ['id' => null, 'name' => $row->subject_area, 'slug' => \Str::slug($row->subject_area)]);
        return response()->json(['data' => $cats]);
    }
}
