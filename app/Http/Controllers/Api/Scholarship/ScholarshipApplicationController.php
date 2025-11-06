<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Scholarship;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScholarshipApplicationResource;
use App\Models\ScholarshipApplication;
use App\Models\ScholarshipApplicationStatusHistory;
use Illuminate\Http\Request;

class ScholarshipApplicationController extends Controller
{
    /**
     * Create new application
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'scholarship_id' => 'required|exists:scholarships,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'date_of_birth' => 'required|date',
            'nationality' => 'required|string',
            'country_of_residence' => 'required|string',
            'address' => 'nullable|string',
            'current_education_level' => 'required|in:high-school,undergraduate,graduate,postgraduate,other',
            'institution_name' => 'required|string',
            'field_of_study' => 'required|string',
            'gpa' => 'nullable|string',
            'graduation_year' => 'nullable|integer|min:1900|max:2100',
            'academic_achievements' => 'nullable|string',
            'research_area' => 'nullable|string',
            'concept_note' => 'nullable|string',
            'motivation_letter' => 'required|string',
            'career_goals' => 'required|string',
            'why_this_scholarship' => 'required|string',
            'financial_need_description' => 'nullable|string',
            'reference_1_name' => 'nullable|string',
            'reference_1_email' => 'nullable|email',
            'reference_2_name' => 'nullable|string',
            'reference_2_email' => 'nullable|email',
            'additional_info' => 'nullable|string',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'transcript' => 'required|file|mimes:pdf|max:5120',
            'motivation_letter_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $application = ScholarshipApplication::create([
            ...$validated,
            'user_id' => $request->user()?->id,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Handle file uploads
        if ($request->hasFile('cv')) {
            $application->addMediaFromRequest('cv')->toMediaCollection('cv');
        }

        if ($request->hasFile('transcript')) {
            $application->addMediaFromRequest('transcript')->toMediaCollection('transcript');
        }

        if ($request->hasFile('motivation_letter_file')) {
            $application->addMediaFromRequest('motivation_letter_file')->toMediaCollection('motivation_letter_file');
        }

        // Add status history
        ScholarshipApplicationStatusHistory::create([
            'application_id' => $application->id,
            'status' => 'submitted',
            'note' => 'Application submitted',
            'timestamp' => now(),
        ]);

        return response()->json([
            'message' => 'Application submitted successfully',
            'id' => $application->id
        ], 201);
    }

    /**
     * Save draft application
     */
    public function saveDraft(Request $request, $id = null)
    {
        $userId = $request->user()?->id;

        if ($id) {
            $application = ScholarshipApplication::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();
            
            $application->update($request->except(['cv', 'transcript', 'motivation_letter_file']));
        } else {
            $application = ScholarshipApplication::create([
                ...$request->except(['cv', 'transcript', 'motivation_letter_file']),
                'user_id' => $userId,
                'status' => 'draft',
            ]);
        }

        return response()->json([
            'message' => 'Draft saved successfully',
            'id' => $application->id
        ]);
    }

    /**
     * Get user's applications
     */
    public function myApplications(Request $request)
    {
        $applications = ScholarshipApplication::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return ScholarshipApplicationResource::collection($applications);
    }

    /**
     * Get single application
     */
    public function show($id)
    {
        $application = ScholarshipApplication::findOrFail($id);

        // Check authorization
        if (request()->user()->id !== $application->user_id) {
            abort(403, 'Unauthorized');
        }

        return new ScholarshipApplicationResource($application);
    }

    /**
     * Get application status history
     */
    public function statusHistory($id)
    {
        $application = ScholarshipApplication::findOrFail($id);

        // Check authorization
        if (request()->user()->id !== $application->user_id) {
            abort(403, 'Unauthorized');
        }

        $history = $application->statusHistory()->orderBy('timestamp', 'asc')->get();

        return response()->json(['data' => $history]);
    }

    /**
     * Admin: Get all applications
     */
    public function adminIndex(Request $request)
    {
        $scholarshipId = $request->input('scholarship_id');
        $status = $request->input('status');

        $query = ScholarshipApplication::query();

        if ($scholarshipId) {
            $query->where('scholarship_id', $scholarshipId);
        }

        if ($status) {
            $query->byStatus($status);
        }

        $applications = $query->orderBy('submitted_at', 'desc')->get();

        return ScholarshipApplicationResource::collection($applications);
    }

    /**
     * Admin: Update application status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,submitted,under-review,shortlisted,interviewed,accepted,rejected,withdrawn',
            'note' => 'nullable|string',
        ]);

        $application = ScholarshipApplication::findOrFail($id);
        $application->update(['status' => $validated['status']]);

        // Add status history
        ScholarshipApplicationStatusHistory::create([
            'application_id' => $application->id,
            'status' => $validated['status'],
            'note' => $validated['note'] ?? null,
            'updated_by' => $request->user()->id,
            'timestamp' => now(),
        ]);

        return response()->json([
            'message' => 'Status updated successfully'
        ]);
    }
}
