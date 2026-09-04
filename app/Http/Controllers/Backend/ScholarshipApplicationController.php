<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BulkDeleteRequest;
use App\Models\ScholarshipApplication;
use App\Models\ScholarshipApplicationStatusHistory;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ScholarshipApplicationController extends Controller
{
    public function index(): Renderable
    {
        $this->authorize('viewAny', ScholarshipApplication::class);

        $breadcrumbs = [
            'title' => __('Scholarship Applications'),
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

        return view('backend.pages.scholarship-applications.index', compact('breadcrumbs'));
    }

    public function show(string $id): Renderable
    {
        $application = ScholarshipApplication::with(['user', 'scholarship', 'evaluations', 'statusHistory'])->findOrFail($id);

        $this->authorize('view', $application);

        $breadcrumbs = [
            'title' => __('Application: :name', ['name' => $application->first_name . ' ' . $application->last_name]),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Applications'),
                    'url' => route('admin.scholarship-applications.index'),
                ],
                [
                    'name' => __('View'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.scholarship-applications.show', compact('application', 'breadcrumbs'));
    }

    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $application = ScholarshipApplication::findOrFail($id);
        
        $this->authorize('update', $application);

        $request->validate([
            'status' => 'required|in:draft,submitted,under-review,shortlisted,interviewed,accepted,rejected,withdrawn',
            'note' => 'nullable|string',
        ]);

        $application->update(['status' => $request->status]);

        // Add status history
        ScholarshipApplicationStatusHistory::create([
            'application_id' => $application->id,
            'status' => $request->status,
            'note' => $request->note,
            'updated_by' => auth()->id(),
            'timestamp' => now(),
        ]);

        return redirect()->back()
            ->with('success', __('Application status updated successfully.'));
    }

    public function destroy(ScholarshipApplication $scholarshipApplication): RedirectResponse
    {
        $this->authorize('delete', $scholarshipApplication);

        $scholarshipApplication->delete();

        return redirect()
            ->route('admin.scholarship-applications.index')
            ->with('success', __('Application deleted successfully.'));
    }

    public function bulkDelete(BulkDeleteRequest $request): RedirectResponse
    {
        $this->authorize('bulkDelete', ScholarshipApplication::class);

        $ids = $request->validated('ids');

        if (empty($ids)) {
            return redirect()->route('admin.scholarship-applications.index')
                ->with('error', __('No applications selected for deletion.'));
        }

        $deletedCount = ScholarshipApplication::whereIn('id', $ids)->delete();

        if ($deletedCount > 0) {
            return redirect()->route('admin.scholarship-applications.index')
                ->with('success', __(':count applications deleted successfully', ['count' => $deletedCount]));
        }

        return redirect()->route('admin.scholarship-applications.index')
            ->with('error', __('No applications were deleted.'));
    }

    public function downloadZip(Request $request, string $batchId)
    {
        $this->authorize('viewAny', ScholarshipApplication::class);

        // Sanitize batchId to prevent directory traversal
        $batchId = preg_replace('/[^a-zA-Z0-9_-]/', '', $batchId);
        $tempZipPath = storage_path('app/public/temp_zip_' . $batchId . '.zip');

        if (!file_exists($tempZipPath)) {
            abort(404, __('ZIP file not found or has already expired.'));
        }

        $finalZipName = 'scholarship_applications_' . now()->format('Y-m-d_H-i-s') . '.zip';

        return response()->download($tempZipPath, $finalZipName)->deleteFileAfterSend(true);
    }
}
