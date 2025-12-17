<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Scholarship;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScholarshipResource;
use App\Models\Scholarship;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    /**
     * List scholarships (public endpoint)
     */
    public function index(Request $request)
    {
        $status = $request->input('status');

        $query = Scholarship::query();

        // Filter by status
        if ($status) {
            $query->byStatus($status);
        }

        $scholarships = $query->orderBy('deadline', 'asc')->get();

        return ScholarshipResource::collection($scholarships);
    }

    /**
     * Get single scholarship
     */
    public function show($id)
    {
        $scholarship = Scholarship::findOrFail($id);
        return new ScholarshipResource($scholarship);
    }
}
