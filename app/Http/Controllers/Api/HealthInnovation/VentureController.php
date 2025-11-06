<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\HealthInnovation;

use App\Http\Controllers\Controller;
use App\Http\Resources\VentureResource;
use App\Models\Venture;
use App\Models\VentureVote;
use Illuminate\Http\Request;

class VentureController extends Controller
{
    /**
     * List ventures (public endpoint)
     */
    public function index(Request $request)
    {
        $perPage = (int) ($request->input('per_page') ?? 15);
        $search = $request->input('search');
        $focusArea = $request->input('focus_area');
        $stage = $request->input('stage');
        $country = $request->input('country');
        $featured = $request->input('featured');
        $sortBy = $request->input('sort_by', 'recent');

        $query = Venture::query()->active();

        // Search
        if ($search) {
            $query->search($search);
        }

        // Focus area filter
        if ($focusArea && $focusArea !== 'all') {
            $query->byFocusArea($focusArea);
        }

        // Stage filter
        if ($stage && $stage !== 'all') {
            $query->where('stage', $stage);
        }

        // Country filter
        if ($country) {
            $query->where('country', $country);
        }

        // Featured filter
        if ($featured) {
            $query->featured();
        }

        // Sorting
        switch ($sortBy) {
            case 'popular':
                $query->orderBy('votes_count', 'desc');
                break;
            case 'alphabetical':
                $query->orderBy('name', 'asc');
                break;
            default: // recent
                $query->orderBy('created_at', 'desc');
                break;
        }

        $ventures = $query->paginate($perPage);

        return VentureResource::collection($ventures);
    }

    /**
     * Get single venture
     */
    public function show($id)
    {
        $venture = Venture::active()->findOrFail($id);
        return new VentureResource($venture);
    }

    /**
     * Vote for a venture
     */
    public function vote(Request $request, $id)
    {
        $venture = Venture::findOrFail($id);
        $userId = $request->user()?->id;
        $ipAddress = $request->ip();

        // Check if already voted
        $existingVote = VentureVote::where('venture_id', $venture->id)
            ->where(function ($query) use ($userId, $ipAddress) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('ip_address', $ipAddress);
                }
            })
            ->first();

        if ($existingVote) {
            return response()->json([
                'message' => 'You have already voted for this venture'
            ], 400);
        }

        // Create vote
        VentureVote::create([
            'venture_id' => $venture->id,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
        ]);

        // Increment votes count
        $venture->increment('votes_count');

        return response()->json([
            'message' => 'Vote recorded successfully',
            'votes_count' => $venture->votes_count
        ]);
    }
}
