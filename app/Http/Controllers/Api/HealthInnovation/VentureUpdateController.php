<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\HealthInnovation;

use App\Http\Controllers\Controller;
use App\Http\Resources\VentureUpdateResource;
use App\Models\VentureUpdate;
use Illuminate\Http\Request;

class VentureUpdateController extends Controller
{
    /**
     * List venture updates (public endpoint)
     */
    public function index(Request $request)
    {
        $perPage = (int) ($request->input('limit') ?? 20);
        $ventureId = $request->input('venture_id');

        $query = VentureUpdate::query()->with('venture');

        // Filter by venture
        if ($ventureId) {
            $query->byVenture($ventureId);
        }

        $updates = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return VentureUpdateResource::collection($updates);
    }
}
