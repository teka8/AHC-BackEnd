<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\ProgramCategory;
use App\Enums\ProgramStatus;
use App\Http\Resources\ProgramResource;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Program::query()
            ->with('media')
            ->where('state', '!=', ProgramStatus::ARCHIVED->value);

        $requestCategory = (string) $request->query('category', '');
        $category = strtolower($requestCategory);

        if (! empty($category)) {
            $enum = ProgramCategory::tryFrom($category);

            if ($enum) {
                $query->forCategory($enum->value);
            }
        }

        $programs = $query->orderByDesc('created_at')->get();

        return ProgramResource::collection($programs);
    }

    public function show(Program $program): JsonResource
    {
        if ($program->state === ProgramStatus::ARCHIVED) {
            abort(404);
        }

        $program->loadMissing('media');

        return ProgramResource::make($program);
    }
}
