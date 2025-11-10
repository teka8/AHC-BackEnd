<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Services\MediaLibraryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Enums\ProgramCategory;

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
            'country' => 'nullable|string|max:255',
            'description' => 'required|string',
            'contact_name' => 'nullable|string|max:255',
            'contact_bio' => 'nullable|string',
            'contact_details' => 'nullable|string',
            'contacts' => ['nullable', 'array'],
            'contacts.*.name' => ['nullable', 'string', 'max:255'],
            'contacts.*.bio' => ['nullable', 'string'],
            'contacts.*.contact' => ['nullable', 'string'],
            'contacts.*.image' => ['nullable', 'image', 'max:5120'],
            'partners_involved' => 'nullable|string',
            'state' => 'sometimes|in:paused,active,archived,upcoming',
            'categories' => ['nullable', 'array'],
            'categories.*' => ['string', Rule::in(array_map(fn ($case) => $case->value, ProgramCategory::cases()))],
        ]);

        $data = $request->only([
            'title',
            'host',
            'country',
            'description',
            'contact_name',
            'contact_bio',
            'contact_details',
            'contacts',
            'partners_involved',
        ]);
        $data['state'] = $request->input('state', 'upcoming');
        $data['categories'] = $this->resolveCategoriesFromRequest($request);

        $data['contact_people'] = $this->resolveContactsFromRequest($request);

        unset($data['contacts']);
        $program = Program::create($data);

        $this->syncProgramImage($request, $program);

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
            'country' => 'nullable|string|max:255',
            'description' => 'required|string',
            'contact_name' => 'nullable|string|max:255',
            'contact_bio' => 'nullable|string',
            'contact_details' => 'nullable|string',
            'contacts' => ['nullable', 'array'],
            'contacts.*.name' => ['nullable', 'string', 'max:255'],
            'contacts.*.bio' => ['nullable', 'string'],
            'contacts.*.contact' => ['nullable', 'string'],
            'contacts.*.image' => ['nullable', 'image', 'max:5120'],
            'partners_involved' => 'nullable|string',
            'state' => 'sometimes|in:paused,active,archived,upcoming',
            'categories' => ['nullable', 'array'],
            'categories.*' => ['string', Rule::in(array_map(fn ($case) => $case->value, ProgramCategory::cases()))],
        ]);

        $data = $request->only([
            'title',
            'host',
            'country',
            'description',
            'contact_name',
            'contact_bio',
            'contact_details',
            'contacts',
            'partners_involved',
        ]);

        // Only update state if it's provided
        if ($request->filled('state')) {
            $data['state'] = $request->input('state');
        }

        $data['categories'] = $this->resolveCategoriesFromRequest($request);
        $data['contact_people'] = $this->resolveContactsFromRequest($request);

        unset($data['contacts']);

        $program->update($data);

        $this->syncProgramImage($request, $program);

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

    private function syncProgramImage(Request $request, Program $program): void
    {
        $imageInput = $request->input('image');

        if (is_array($imageInput)) {
            $imageInput = $imageInput[0] ?? null;
        }

        $hasReplacement = $request->hasFile('image') || ($imageInput !== null && $imageInput !== '');

        if ($request->boolean('remove_image') && ! $hasReplacement) {
            $program->clearMediaCollection('featured');
            $program->forceFill(['image' => null])->save();

            return;
        }

        if ($request->hasFile('image')) {
            $program->clearMediaCollection('featured');
            $media = $program->addMediaFromRequest('image')->toMediaCollection('featured');
            $program->forceFill(['image' => $media?->id])->save();

            return;
        }

        if ($imageInput === null || $imageInput === '') {
            return;
        }

        if (is_numeric($imageInput)) {
            $program->clearMediaCollection('featured');
            $media = $this->mediaService->associateExistingMedia($program, (string) $imageInput, 'featured');
            $program->forceFill(['image' => $media?->id ?? (int) $imageInput])->save();

            return;
        }

        $program->forceFill(['image' => $imageInput])->save();
    }

    private function resolveCategoriesFromRequest(Request $request): array
    {
        $categories = collect($request->input('categories', []))
            ->flatMap(fn ($cat) => is_array($cat) ? $cat : [$cat])
            ->filter()
            ->map(fn ($cat) => is_string($cat) ? strtolower(trim($cat)) : (string) $cat)
            ->map(fn ($cat) => ProgramCategory::tryFrom($cat))
            ->filter()
            ->map(fn (ProgramCategory $category) => $category->value)
            ->unique()
            ->values();

        if ($categories->contains(ProgramCategory::UNCATEGORIZED->value)) {
            $categories = $categories
                ->reject(fn ($cat) => $cat === ProgramCategory::UNCATEGORIZED->value)
                ->values();
        }

        if ($categories->isEmpty()) {
            return [ProgramCategory::UNCATEGORIZED->value];
        }

        return $categories->all();
    }

    private function resolveContactsFromRequest(Request $request): array
    {
        $inputContacts = collect($request->input('contacts', []));
        $fileContacts = $request->file('contacts', []);

        return $inputContacts
            ->map(function ($contact, $index) use ($fileContacts) {
                $normalized = [
                    'name' => isset($contact['name']) ? (string) $contact['name'] : '',
                    'bio' => isset($contact['bio']) ? (string) $contact['bio'] : '',
                    'contact' => isset($contact['contact']) ? (string) $contact['contact'] : '',
                ];

                $imagePath = isset($contact['existing_image']) ? (string) $contact['existing_image'] : null;
                $imagePath = is_string($imagePath) ? trim($imagePath) : null;

                if (isset($fileContacts[$index]['image']) && $fileContacts[$index]['image']) {
                    $imageFile = $fileContacts[$index]['image'];
                    $imagePath = $imageFile->store('program-contacts', 'public');
                }

                return [
                    'name' => trim($normalized['name']),
                    'bio' => trim($normalized['bio']),
                    'contact' => trim($normalized['contact']),
                    'image' => $imagePath,
                ];
            })
            ->filter(fn ($contact) => $contact['name'] !== '' || $contact['bio'] !== '' || $contact['contact'] !== '')
            ->values()
            ->all();
    }
}
