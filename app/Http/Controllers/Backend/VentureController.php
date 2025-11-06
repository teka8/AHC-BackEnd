<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BulkDeleteRequest;
use App\Models\Venture;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VentureController extends Controller
{
    public function index(): Renderable
    {
        $this->authorize('viewAny', Venture::class);

        $breadcrumbs = [
            'title' => __('Health Innovation Ventures'),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Ventures'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.ventures.index', compact('breadcrumbs'));
    }

    public function create(): Renderable
    {
        $this->authorize('create', Venture::class);

        $breadcrumbs = [
            'title' => __('Create New Venture'),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Ventures'),
                    'url' => route('admin.ventures.index'),
                ],
                [
                    'name' => __('Create'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.ventures.create', compact('breadcrumbs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Venture::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'description' => 'required|string',
            'focus_area' => 'required|in:mental-health,telemedicine,pharmaceuticals,biotech,medtech,diagnostics,health-tech,other',
            'stage' => 'required|in:idea,prototype,early-stage,growth,scale',
            'founded_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'country' => 'required|string',
            'website' => 'nullable|url',
            'social_links' => 'nullable|array',
            'founders' => 'required|string',
            'team_size' => 'nullable|integer|min:1',
            'funding_raised' => 'nullable|numeric|min:0',
            'patients_impacted' => 'nullable|integer|min:0',
            'countries_reached' => 'nullable|integer|min:0',
            'featured' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ]);

        $venture = Venture::create($validated);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $venture->addMediaFromRequest('logo')->toMediaCollection('logo');
        }

        // Handle images upload
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $venture->addMedia($image)->toMediaCollection('images');
            }
        }

        return redirect()
            ->route('admin.ventures.index')
            ->with('success', __('Venture created successfully.'));
    }

    public function edit(Venture $venture): Renderable
    {
        $this->authorize('update', $venture);

        $breadcrumbs = [
            'title' => __('Edit Venture: :name', ['name' => $venture->name]),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Ventures'),
                    'url' => route('admin.ventures.index'),
                ],
                [
                    'name' => __('Edit'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.ventures.edit', compact('venture', 'breadcrumbs'));
    }

    public function update(Request $request, Venture $venture): RedirectResponse
    {
        $this->authorize('update', $venture);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'description' => 'required|string',
            'focus_area' => 'required|in:mental-health,telemedicine,pharmaceuticals,biotech,medtech,diagnostics,health-tech,other',
            'stage' => 'required|in:idea,prototype,early-stage,growth,scale',
            'founded_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'country' => 'required|string',
            'website' => 'nullable|url',
            'social_links' => 'nullable|array',
            'founders' => 'required|string',
            'team_size' => 'nullable|integer|min:1',
            'funding_raised' => 'nullable|numeric|min:0',
            'patients_impacted' => 'nullable|integer|min:0',
            'countries_reached' => 'nullable|integer|min:0',
            'featured' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ]);

        $venture->update($validated);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $venture->clearMediaCollection('logo');
            $venture->addMediaFromRequest('logo')->toMediaCollection('logo');
        }

        // Handle images upload
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $venture->addMedia($image)->toMediaCollection('images');
            }
        }

        return redirect()
            ->route('admin.ventures.index')
            ->with('success', __('Venture updated successfully.'));
    }

    public function show(string $id): Renderable
    {
        $venture = Venture::with(['updates', 'votes'])->findOrFail($id);

        $this->authorize('view', $venture);

        $breadcrumbs = [
            'title' => __('View Venture: :name', ['name' => $venture->name]),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Ventures'),
                    'url' => route('admin.ventures.index'),
                ],
                [
                    'name' => __('View'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.ventures.show', compact('venture', 'breadcrumbs'));
    }

    public function destroy(Venture $venture): RedirectResponse
    {
        $this->authorize('delete', $venture);

        $venture->delete();

        return redirect()
            ->route('admin.ventures.index')
            ->with('success', __('Venture deleted successfully.'));
    }

    public function bulkDelete(BulkDeleteRequest $request): RedirectResponse
    {
        $this->authorize('bulkDelete', Venture::class);

        $ids = $request->validated('ids');

        if (empty($ids)) {
            return redirect()->route('admin.ventures.index')
                ->with('error', __('No ventures selected for deletion.'));
        }

        $deletedCount = Venture::whereIn('id', $ids)->delete();

        if ($deletedCount > 0) {
            return redirect()->route('admin.ventures.index')
                ->with('success', __(':count ventures deleted successfully', ['count' => $deletedCount]));
        }

        return redirect()->route('admin.ventures.index')
            ->with('error', __('No ventures were deleted.'));
    }

    public function toggleFeatured(Request $request, $id)
    {
        $venture = Venture::findOrFail($id);
        
        $this->authorize('update', $venture);

        $venture->featured = !$venture->featured;
        $venture->save();

        return response()->json([
            'success' => true,
            'featured' => $venture->featured,
            'message' => $venture->featured 
                ? __('Venture marked as featured') 
                : __('Venture removed from featured')
        ]);
    }
}
