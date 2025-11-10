<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\ProgramStatus;
use App\Http\Resources\ProgramResource;
use App\Models\Program;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProgramController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $programs = Program::query()
            ->with('media')
            ->where('state', '!=', ProgramStatus::ARCHIVED->value)
            ->orderByDesc('created_at')
            ->get();

        return ProgramResource::collection($programs);
    }
}
