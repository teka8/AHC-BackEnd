<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AhcLeaderResource;
use App\Models\AhcLeader;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class AhcLeaderController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $leaders = AhcLeader::query()
            ->active()
            ->ordered()
            ->get();

        return AhcLeaderResource::collection($leaders);
    }

    public function show(AhcLeader $ahcLeader): JsonResource
    {
        if (! $ahcLeader->is_active) {
            abort(404);
        }

        return AhcLeaderResource::make($ahcLeader);
    }
}
