<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Enums\ProgramStatus; // Import the ProgramStatus enum
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProgramController extends Controller
{
    public function index(): JsonResponse
    {
        $programs = Program::where('state', '!=', ProgramStatus::ARCHIVED)->get();

        return response()->json($programs);
    }
}
