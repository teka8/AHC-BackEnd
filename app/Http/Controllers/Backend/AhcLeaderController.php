<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AhcLeader;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AhcLeaderController extends Controller
{
    public function index(): Renderable
    {
        $this->authorize('viewAny', AhcLeader::class);

        $breadcrumbs = [
            'title' => __('AHC Leaders & Teams'),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('AHC Leaders & Teams'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.ahc-leaders.index', compact('breadcrumbs'));
    }

    public function create(): Renderable
    {
        $this->authorize('create', AhcLeader::class);

        $breadcrumbs = [
            'title' => __('Create New Leader or Team Member'),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('AHC Leaders & Teams'),
                    'url' => route('admin.ahc-leaders.index'),
                ],
                [
                    'name' => __('Create'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.ahc-leaders.create', compact('breadcrumbs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', AhcLeader::class);

        $validated = $request->validate([
            'type' => 'required|in:leader,team',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'description' => 'nullable|string',
            'linkedin_url' => 'nullable|url|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('ahc-leaders', 'public');
            $validated['image'] = $imagePath;
        }

        AhcLeader::create($validated);

        $typeLabel = $validated['type'] === 'leader' ? __('Leader') : __('Team member');

        return redirect()
            ->route('admin.ahc-leaders.index')
            ->with('success', __(':type created successfully.', ['type' => $typeLabel]));
    }

    public function show(AhcLeader $ahcLeader): Renderable
    {
        $this->authorize('view', $ahcLeader);

        $typeLabel = $ahcLeader->type === 'leader' ? __('Leader') : __('Team Member');

        $breadcrumbs = [
            'title' => __(':type: :name', ['type' => $typeLabel, 'name' => $ahcLeader->name]),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('AHC Leaders & Teams'),
                    'url' => route('admin.ahc-leaders.index'),
                ],
                [
                    'name' => __('View'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.ahc-leaders.show', compact('ahcLeader', 'breadcrumbs'));
    }

    public function edit(AhcLeader $ahcLeader): Renderable
    {
        $this->authorize('update', $ahcLeader);

        $typeLabel = $ahcLeader->type === 'leader' ? __('Leader') : __('Team Member');

        $breadcrumbs = [
            'title' => __('Edit :type: :name', ['type' => $typeLabel, 'name' => $ahcLeader->name]),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('AHC Leaders & Teams'),
                    'url' => route('admin.ahc-leaders.index'),
                ],
                [
                    'name' => __('Edit'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.ahc-leaders.edit', compact('ahcLeader', 'breadcrumbs'));
    }

    public function update(Request $request, AhcLeader $ahcLeader): RedirectResponse
    {
        $this->authorize('update', $ahcLeader);

        $validated = $request->validate([
            'type' => 'required|in:leader,team',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'description' => 'nullable|string',
            'linkedin_url' => 'nullable|url|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            if ($ahcLeader->image && Storage::disk('public')->exists($ahcLeader->image)) {
                Storage::disk('public')->delete($ahcLeader->image);
            }
            $imagePath = $request->file('image')->store('ahc-leaders', 'public');
            $validated['image'] = $imagePath;
        }

        if ($request->boolean('remove_image') && ! $request->hasFile('image')) {
            if ($ahcLeader->image && Storage::disk('public')->exists($ahcLeader->image)) {
                Storage::disk('public')->delete($ahcLeader->image);
            }
            $validated['image'] = null;
        }

        $ahcLeader->update($validated);

        $typeLabel = $validated['type'] === 'leader' ? __('Leader') : __('Team member');

        return redirect()
            ->route('admin.ahc-leaders.index')
            ->with('success', __(':type updated successfully.', ['type' => $typeLabel]));
    }

    public function destroy(AhcLeader $ahcLeader): RedirectResponse
    {
        $this->authorize('delete', $ahcLeader);

        if ($ahcLeader->image && Storage::disk('public')->exists($ahcLeader->image)) {
            Storage::disk('public')->delete($ahcLeader->image);
        }

        $ahcLeader->delete();

        return redirect()
            ->route('admin.ahc-leaders.index')
            ->with('success', __('Leader deleted successfully.'));
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $this->authorize('bulkDelete', AhcLeader::class);

        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('admin.ahc-leaders.index')
                ->with('error', __('No leaders selected for deletion.'));
        }

        $leaders = AhcLeader::whereIn('id', $ids)->get();

        foreach ($leaders as $leader) {
            if ($leader->image && Storage::disk('public')->exists($leader->image)) {
                Storage::disk('public')->delete($leader->image);
            }
        }

        $deletedCount = AhcLeader::whereIn('id', $ids)->delete();

        if ($deletedCount > 0) {
            return redirect()->route('admin.ahc-leaders.index')
                ->with('success', __(':count leaders deleted successfully', ['count' => $deletedCount]));
        }

        return redirect()->route('admin.ahc-leaders.index')
            ->with('error', __('No leaders were deleted.'));
    }
}
