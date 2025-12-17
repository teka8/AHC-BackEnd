<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BulkDeleteRequest;
use App\Models\ScholarshipEvaluation;
use App\Models\ScholarshipApplication;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ScholarshipEvaluationController extends Controller
{
    public function index(): Renderable
    {
        $breadcrumbs = [
            'title' => __('Scholarship Evaluations'),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Evaluations'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.scholarship-evaluation.index', compact('breadcrumbs'));
    }

    public function create(string $application): Renderable
    {
        $application = ScholarshipApplication::with(['scholarship', 'user'])->findOrFail($application);

        $breadcrumbs = [
            'title' => __('Create Evaluation'),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Evaluations'),
                    'url' => route('admin.scholarship-evaluation.index'),
                ],
                [
                    'name' => __('Create'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.scholarship-evaluation.create', compact('application', 'breadcrumbs'));
    }

    public function store(Request $request, string $application): RedirectResponse
    {
        $application = ScholarshipApplication::findOrFail($application);

        $validated = $request->validate([
            'academic_performance_score' => 'required|numeric|min:0|max:10',
            'motivation_score' => 'required|numeric|min:0|max:10',
            'research_quality_score' => 'required|numeric|min:0|max:10',
            'financial_need_score' => 'required|numeric|min:0|max:10',
            'overall_score' => 'required|numeric|min:0|max:10',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'recommendation' => 'required|in:strong-accept,accept,waitlist,reject',
            'notes' => 'nullable|string',
        ]);

        $validated['application_id'] = $application->id;
        $validated['reviewer_id'] = auth()->id();

        $evaluation = ScholarshipEvaluation::create($validated);

        return redirect()
            ->route('admin.scholarship-evaluation.show', $evaluation)
            ->with('success', __('Evaluation created successfully.'));
    }

    public function show(string $id): Renderable
    {
        $evaluation = ScholarshipEvaluation::with(['application.scholarship', 'application.user', 'reviewer'])->findOrFail($id);

        $breadcrumbs = [
            'title' => __('Evaluation Details'),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Evaluations'),
                    'url' => route('admin.scholarship-evaluation.index'),
                ],
                [
                    'name' => __('View'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.scholarship-evaluation.show', compact('evaluation', 'breadcrumbs'));
    }

    public function edit(string $id): Renderable
    {
        $evaluation = ScholarshipEvaluation::with(['application.scholarship', 'application.user', 'reviewer'])->findOrFail($id);

        $breadcrumbs = [
            'title' => __('Edit Evaluation'),
            'links' => [
                [
                    'name' => __('Home'),
                    'url' => route('admin.dashboard'),
                ],
                [
                    'name' => __('Evaluations'),
                    'url' => route('admin.scholarship-evaluation.index'),
                ],
                [
                    'name' => __('Edit'),
                    'url' => '#',
                ],
            ],
        ];

        return view('backend.pages.scholarship-evaluation.edit', compact('evaluation', 'breadcrumbs'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $evaluation = ScholarshipEvaluation::findOrFail($id);

        $validated = $request->validate([
            'academic_performance_score' => 'required|numeric|min:0|max:10',
            'motivation_score' => 'required|numeric|min:0|max:10',
            'research_quality_score' => 'required|numeric|min:0|max:10',
            'financial_need_score' => 'required|numeric|min:0|max:10',
            'overall_score' => 'required|numeric|min:0|max:10',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'recommendation' => 'required|in:strong-accept,accept,waitlist,reject',
            'notes' => 'nullable|string',
        ]);

        $evaluation->update($validated);

        return redirect()
            ->route('admin.scholarship-evaluation.show', $evaluation)
            ->with('success', __('Evaluation updated successfully.'));
    }

    public function destroy(ScholarshipEvaluation $scholarshipEvaluation): RedirectResponse
    {
        $scholarshipEvaluation->delete();

        return redirect()
            ->route('admin.scholarship-evaluation.index')
            ->with('success', __('Evaluation deleted successfully.'));
    }

    public function bulkDelete(BulkDeleteRequest $request): RedirectResponse
    {
        $ids = $request->validated('ids');

        if (empty($ids)) {
            return redirect()->route('admin.scholarship-evaluation.index')
                ->with('error', __('No evaluations selected for deletion.'));
        }

        $deletedCount = ScholarshipEvaluation::whereIn('id', $ids)->delete();

        if ($deletedCount > 0) {
            return redirect()->route('admin.scholarship-evaluation.index')
                ->with('success', __(':count evaluations deleted successfully', ['count' => $deletedCount]));
        }

        return redirect()->route('admin.scholarship-evaluation.index')
            ->with('error', __('No evaluations were deleted.'));
    }
}
