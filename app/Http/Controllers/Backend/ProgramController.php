<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use App\Services\MediaLibraryService;

class ProgramController extends Controller
{
    public function __construct(private readonly MediaLibraryService $mediaService)
    {
        $this->authorizeResource(Program::class, 'program');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Program::class);
        
        $breadcrumbs = [
            ['name' => 'Programs', 'route' => route('admin.programs.index')],
        ];
        return view('backend.pages.programs.index', compact('breadcrumbs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Program::class);
        
        $breadcrumbs = [
            ['name' => 'Programs', 'route' => route('admin.programs.index')],
            ['name' => 'Create'],
        ];
        return view('backend.pages.programs.create', compact('breadcrumbs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Program::class);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'description' => 'required|string',
            'state' => 'sometimes|in:paused,active,archived,upcoming',
        ]);

        $data = $request->all();
        $data['state'] = $data['state'] ?? 'upcoming';

        $program = Program::create($data);

        if ($request->hasFile('image')) {
            $program->addMediaFromRequest('image')->toMediaCollection('programs');
        } elseif ($request->filled('media_id')) {
            $this->mediaService->associateExistingMedia($program, $request->input('media_id'), 'programs');
        }


        return redirect()->route('admin.programs.index')->with('success', 'Program created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        $this->authorize('view', $program);
        
        $breadcrumbs = [
            ['name' => 'Programs', 'route' => route('admin.programs.index')],
            ['name' => $program->title],
        ];
        
        return view('backend.pages.programs.show', compact('program', 'breadcrumbs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
        $this->authorize('update', $program);
        
        $breadcrumbs = [
            ['name' => 'Programs', 'route' => route('admin.programs.index')],
            ['name' => 'Edit'],
        ];
        return view('backend.pages.programs.edit', compact('program', 'breadcrumbs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Program $program)
    {
        $this->authorize('update', $program);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'description' => 'required|string',
            'state' => 'sometimes|in:paused,active,archived,upcoming',
        ]);
        
        $data = $request->only(['title', 'host', 'description']);
        
        // Only update state if it's provided
        if ($request->filled('state')) {
            $data['state'] = $request->input('state');
        }
        
        $program->update($data);

        if ($request->hasFile('image')) {
            $program->clearMediaCollection('programs');
            $program->addMediaFromRequest('image')->toMediaCollection('programs');
        } elseif ($request->filled('media_id')) {
            $program->clearMediaCollection('programs');
            $this->mediaService->associateExistingMedia($program, $request->input('media_id'), 'programs');
        }

        return redirect()->route('admin.programs.index')->with('success', 'Program updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        $this->authorize('delete', $program);
        
        $program->delete();

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program deleted successfully.');
    }

    /**
     * Change the status of the specified program.
     */
    public function changeStatus(Request $request, Program $program)
    {
        $this->authorize('update', $program);
        
        $action = $request->input('action');

        if ($program->changeStatus($action)) {
            return response()->json(['message' => 'Program status updated successfully.'], 200);
        }

        return response()->json(['message' => 'Failed to update program status.'], 400);
    }
}
