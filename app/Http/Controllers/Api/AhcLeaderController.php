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
        $query = AhcLeader::query()
            ->active()
            ->ordered();

        $type = request()->query('type');
        if ($type === 'leader') {
            $query->leaders();
        } elseif ($type === 'team') {
            $query->team();
        }

        return AhcLeaderResource::collection($query->get());
    }

    public function show(AhcLeader $ahcLeader): JsonResource
    {
        if (! $ahcLeader->is_active) {
            abort(404);
        }

        return AhcLeaderResource::make($ahcLeader);
    }
}
