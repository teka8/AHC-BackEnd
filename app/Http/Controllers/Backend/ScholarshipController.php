<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Events\NewContentPublished;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BulkDeleteRequest;
use App\Models\Scholarship;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ScholarshipController extends Controller
{
    public function index(): Renderable
    {
        $this->authorize('viewAny', Scholarship::class);

        $breadcrumbs = [
            'title' => __('Scholarships'),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Scholarships'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.scholarships.index', compact('breadcrumbs'));
    }

    public function create(): Renderable
    {
        $this->authorize('create', Scholarship::class);

        $breadcrumbs = [
            'title' => __('Create New Scholarship'),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Scholarships'),
                    'url' => route('admin.scholarships.index'),
                ],
                [
                    'name' => __('Create'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.scholarships.create', compact('breadcrumbs'));
    }

    public function store(Request $request): RedirectResponse
    {
        
        $this->authorize('create', Scholarship::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'program_type' => 'required|in:undergraduate,graduate,postgraduate,research',
            'eligibility_criteria' => 'required|string',
            'required_documents' => 'required|array',
            'benefits' => 'required|array',
            'coverage' => 'required|string',
            'amount' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date',
            'application_start_date' => 'nullable|date',
            'status' => 'required|in:open,closed,upcoming',
            'available_slots' => 'nullable|integer|min:1',
            'scholarship_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max

        ]);

        /**
         *  Handle Event Image Upload (Single)
         */
        if ($request->hasFile('scholarship_image')) {
            $imagePath = $request->file('scholarship_image')->store('scholarship-images', 'public');
            $validated['scholarship_image'] = $imagePath;
        }
        
        $scholarship = Scholarship::create($validated);

        if ($scholarship->status === 'open') {
            event(new NewContentPublished($scholarship, 'scholarship'));
        }

        return redirect()
            ->route('admin.scholarships.index')
            ->with('success', __('Scholarship created successfully.'));
    }

    public function edit(Scholarship $scholarship): Renderable
    {
        $this->authorize('update', $scholarship);

        $breadcrumbs = [
            'title' => __('Edit Scholarship: :title', ['title' => $scholarship->title]),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Scholarships'),
                    'url' => route('admin.scholarships.index'),
                ],
                [
                    'name' => __('Edit'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.scholarships.edit', compact('scholarship', 'breadcrumbs'));
    }

    public function update(Request $request, Scholarship $scholarship): RedirectResponse
    {
        $this->authorize('update', $scholarship);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'program_type' => 'required|in:undergraduate,graduate,postgraduate,research',
            'eligibility_criteria' => 'required|string',
            'required_documents' => 'required|array',
            'benefits' => 'required|array',
            'coverage' => 'required|string',
            'amount' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date',
            'application_start_date' => 'nullable|date',
            'status' => 'required|in:open,closed,upcoming',
            'available_slots' => 'nullable|integer|min:1',
            'scholarship_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max

        ]);

        // Handle scholarship image upload
        if ($request->hasFile('scholarship_image')) {
            // Delete old image if exists
            if ($scholarship->scholarship_image) {
                Storage::disk('public')->delete($scholarship->scholarship_image);
            }
            
            $imagePath = $request->file('scholarship_image')->store('scholarship-images', 'public');
            $validated['scholarship_image'] = $imagePath;
        }

        $scholarship->update($validated);

        if ($scholarship->status === 'open' && $scholarship->wasChanged('status')) {
            event(new NewContentPublished($scholarship, 'scholarship'));
        }

        return redirect()
            ->route('admin.scholarships.index')
            ->with('success', __('Scholarship updated successfully.'));
    }

    public function show(string $id): Renderable
    {
        $scholarship = Scholarship::with(['applications'])->findOrFail($id);

        $this->authorize('view', $scholarship);

        $breadcrumbs = [
            'title' => __('Scholarship: :title', ['title' => $scholarship->title]),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Scholarships'),
                    'url' => route('admin.scholarships.index'),
                ],
                [
                    'name' => __('View'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.scholarships.show', compact('scholarship', 'breadcrumbs'));
    }

    public function destroy(Scholarship $scholarship): RedirectResponse
    {
        $this->authorize('delete', $scholarship);

        $scholarship->delete();

        return redirect()
            ->route('admin.scholarships.index')
            ->with('success', __('Scholarship deleted successfully.'));
    }

    public function bulkDelete(BulkDeleteRequest $request): RedirectResponse
    {
        $this->authorize('bulkDelete', Scholarship::class);

        $ids = $request->validated('ids');

        if (empty($ids)) {
            return redirect()->route('admin.scholarships.index')
                ->with('error', __('No scholarships selected for deletion.'));
        }

        $deletedCount = Scholarship::whereIn('id', $ids)->delete();

        if ($deletedCount > 0) {
            return redirect()->route('admin.scholarships.index')
                ->with('success', __(':count scholarships deleted successfully', ['count' => $deletedCount]));
        }

        return redirect()->route('admin.scholarships.index')
            ->with('error', __('No scholarships were deleted.'));
    }
}
