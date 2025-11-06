<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BulkDeleteRequest;
use App\Models\VentureApplication;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VentureApplicationController extends Controller
{
    public function index(): Renderable
    {
        $this->authorize('viewAny', VentureApplication::class);

        $breadcrumbs = [
            'title' => __('Venture Applications'),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Applications'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.venture-applications.index', compact('breadcrumbs'));
    }

    public function show(string $id): Renderable
    {
        $application = VentureApplication::with(['user'])->findOrFail($id);

        $this->authorize('view', $application);

        $breadcrumbs = [
            'title' => __('Application: :name', ['name' => $application->venture_name]),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Applications'),
                    'url' => route('admin.venture-applications.index'),
                ],
                [
                    'name' => __('View'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.venture-applications.show', compact('application', 'breadcrumbs'));
    }

    public function updateStatus(Request $request, VentureApplication $ventureApplication): RedirectResponse
    {
        $this->authorize('update', $ventureApplication);

        $request->validate([
            'status' => 'required|in:draft,submitted,under-review,approved,rejected',
        ]);

        $ventureApplication->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', __('Application status updated successfully.'));
    }

    public function destroy(VentureApplication $ventureApplication): RedirectResponse
    {
        $this->authorize('delete', $ventureApplication);

        $ventureApplication->delete();

        return redirect()
            ->route('admin.venture-applications.index')
            ->with('success', __('Application deleted successfully.'));
    }

    public function bulkDelete(BulkDeleteRequest $request): RedirectResponse
    {
        $this->authorize('bulkDelete', VentureApplication::class);

        $ids = $request->validated('ids');

        if (empty($ids)) {
            return redirect()->route('admin.venture-applications.index')
                ->with('error', __('No applications selected for deletion.'));
        }

        $deletedCount = VentureApplication::whereIn('id', $ids)->delete();

        if ($deletedCount > 0) {
            return redirect()->route('admin.venture-applications.index')
                ->with('success', __(':count applications deleted successfully', ['count' => $deletedCount]));
        }

        return redirect()->route('admin.venture-applications.index')
            ->with('error', __('No applications were deleted.'));
    }
}
