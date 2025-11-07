<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\HealthInnovation;

use App\Http\Controllers\Controller;
use App\Models\VentureApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VentureApplicationController extends Controller
{
    /**
     * Create new application
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'venture_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'description' => 'required|string',
            'focus_area' => 'required|in:mental-health,telemedicine,pharmaceuticals,biotech,medtech,diagnostics,health-tech,other',
            'stage' => 'required|in:idea,prototype,early-stage,growth,scale',
            'founded_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'country' => 'required|string',
            'website' => 'nullable|url',
            'contact_name' => 'required|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string',
            'founders' => 'required|string',
            'team_size' => 'nullable|integer|min:1',
            'team_description' => 'nullable|string',
            'problem_statement' => 'required|string',
            'solution_description' => 'required|string',
            'target_market' => 'required|string',
            'unique_value_proposition' => 'required|string',
            'current_stage_description' => 'required|string',
            'patients_served' => 'nullable|integer|min:0',
            'revenue_generated' => 'nullable|numeric|min:0',
            'funding_raised' => 'nullable|numeric|min:0',
            'key_milestones' => 'nullable|string',
            'funding_sought' => 'nullable|numeric|min:0',
            'use_of_funds' => 'nullable|string',
            'why_apply' => 'required|string',
            'additional_info' => 'nullable|string',
            'pitch_deck' => 'required|file|mimes:pdf,ppt,pptx|max:10240',
            'business_plan' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        // Handle file uploads
        $pitchDeckPath = null;
        $businessPlanPath = null;

        if ($request->hasFile('pitch_deck')) {
            $pitchDeckPath = $request->file('pitch_deck')->store('applications/pitch-decks', 'public');
        }

        if ($request->hasFile('business_plan')) {
            $businessPlanPath = $request->file('business_plan')->store('applications/business-plans', 'public');
        }

        $application = VentureApplication::create([
            ...$validated,
            'user_id' => $request->user()?->id,
            'status' => 'submitted',
            'pitch_deck' => $pitchDeckPath,
            'business_plan' => $businessPlanPath,
            'submitted_at' => now(),
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
            $application = VentureApplication::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();
            
            $application->update($request->except(['pitch_deck', 'business_plan']));
        } else {
            $application = VentureApplication::create([
                ...$request->except(['pitch_deck', 'business_plan']),
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
        $applications = VentureApplication::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $applications]);
    }

    /**
     * Get single application
     */
    public function show($id)
    {
        $application = VentureApplication::findOrFail($id);

        // Check authorization
        if (request()->user()->id !== $application->user_id) {
            abort(403, 'Unauthorized');
        }

        return response()->json(['data' => $application]);
    }
}
