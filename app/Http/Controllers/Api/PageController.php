<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * List published pages (no auth required).
     */
    public function index(Request $request)
    {
        $perPage = (int) ($request->input('per_page') ?? 10);
        $search = $request->input('search');

        // Filters
        $section = $request->input('section');
        $showInNav = $request->input('show_in_nav');
        $showInFooter = $request->input('show_in_footer');
        $isCustomSection = $request->input('is_custom_section');

        $query = Page::query()->where('status', 'published');

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('section', 'like', "%{$search}%");
            });
        }

        // Filter by section
        if ($section) {
            $query->where('section', $section);
        }

        // Filter by navigation visibility
        if ($showInNav !== null) {
            $query->where('show_in_nav', (bool) $showInNav);
        }

        // Filter by footer visibility
        if ($showInFooter !== null) {
            $query->where('show_in_footer', (bool) $showInFooter);
        }

        // Filter by custom section
        if ($isCustomSection !== null) {
            $query->where('is_custom_section', (bool) $isCustomSection);
        }

        // Sorting options
        $sortBy = $request->input('sort_by', 'title');
        $sortOrder = $request->input('sort_order', 'asc');
        
        $allowedSortFields = ['title', 'section', 'created_at', 'updated_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('title', 'asc');
        }

        $pages = $query->paginate($perPage);

        return PageResource::collection($pages);
    }

    /**
     * Get single published page by ID.
     */
    public function show($id)
    {
        // Fetch a single published page by ID
        $page = Page::where('status', 'published')->findOrFail($id);

        return response()->json([
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'content' => $page->content,
                'section' => $page->section,
                'is_custom_section' => $page->is_custom_section,
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
                'show_in_nav' => $page->show_in_nav,
                'show_in_footer' => $page->show_in_footer,
                'status' => $page->status,
                'created_at' => $page->created_at,
                'updated_at' => $page->updated_at,
                'url' => $page->url, // This uses the accessor from your Page model
                'created_by' => $page->createdBy ? [
                    'id' => $page->createdBy->id,
                    'name' => $page->createdBy->name,
                    'email' => $page->createdBy->email,
                ] : null,
                'updated_by' => $page->updatedBy ? [
                    'id' => $page->updatedBy->id,
                    'name' => $page->updatedBy->name,
                    'email' => $page->updatedBy->email,
                ] : null,
            ]
        ]);
    }

    /**
     * Get published page by slug.
     */
    public function showBySlug($slug)
    {
        // Fetch a single published page by slug
        $page = Page::where('status', 'published')
                    ->where('slug', $slug)
                    ->firstOrFail();

        return response()->json([
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'content' => $page->content,
                'section' => $page->section,
                'is_custom_section' => $page->is_custom_section,
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
                'show_in_nav' => $page->show_in_nav,
                'show_in_footer' => $page->show_in_footer,
                'status' => $page->status,
                'created_at' => $page->created_at,
                'updated_at' => $page->updated_at,
                'url' => $page->url,
                'created_by' => $page->createdBy ? [
                    'id' => $page->createdBy->id,
                    'name' => $page->createdBy->name,
                    'email' => $page->createdBy->email,
                ] : null,
                'updated_by' => $page->updatedBy ? [
                    'id' => $page->updatedBy->id,
                    'name' => $page->updatedBy->name,
                    'email' => $page->updatedBy->email,
                ] : null,
            ]
        ]);
    }

    /**
     * Get navigation pages (pages visible in navigation).
     */
    public function navigation()
    {
        try {
            $pages = Page::where('status', 'published')
                        ->where('show_in_nav', true)
                        ->orderBy('title')
                        ->get();

            return response()->json([
                'pages' => $pages->map(function ($page) {
                    return [
                        'id' => $page->id,
                        'title' => $page->title,
                        'slug' => $page->slug,
                        'section' => $page->section,
                        'url' => $page->url,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Navigation fetch error: ' . $e->getMessage());
            return response()->json(['pages' => []]);
        }
    }

    /**
     * Get footer pages (pages visible in footer).
     */
    public function footer()
    {
        try {
            $pages = Page::where('status', 'published')
                        ->where('show_in_footer', true)
                        ->orderBy('title')
                        ->get();

            return response()->json([
                'pages' => $pages->map(function ($page) {
                    return [
                        'id' => $page->id,
                        'title' => $page->title,
                        'slug' => $page->slug,
                        'section' => $page->section,
                        'url' => $page->url,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Footer fetch error: ' . $e->getMessage());
            return response()->json(['pages' => []]);
        }
    }

    /**
     * Get pages by section.
     */
    public function bySection($section)
    {
        $pages = Page::where('status', 'published')
                    ->where('section', $section)
                    ->orderBy('title')
                    ->get();

        return response()->json([
            'section' => $section,
            'pages' => $pages->map(function ($page) {
                return [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'content' => $page->content,
                    'meta_title' => $page->meta_title,
                    'meta_description' => $page->meta_description,
                    'url' => $page->url,
                    'created_at' => $page->created_at,
                    'updated_at' => $page->updated_at,
                ];
            })
        ]);
    }
}