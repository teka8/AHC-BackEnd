<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Enums\Hooks\EventActionHook;
use App\Enums\Hooks\EventFilterHook;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BulkDeleteRequest;
use App\Models\Event;
use App\Support\Facades\Hook;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\EventService;
use Illuminate\Http\RedirectResponse;

class EventController extends Controller
{
    public function __construct(
        private readonly EventService $eventService,
    ) {
    }
    public function index(): Renderable
    {
        $this->authorize('viewAny', Event::class);

        $breadcrumbs = [
            ['name' => __('Events'), 'url' => route('admin.events.index')],
        ];

        return view('backend.pages.events.index', compact('breadcrumbs'));
    }

    public function create(): Renderable
    {
        $this->authorize('create', Event::class);

        $breadcrumbs = [
            ['name' => __('Events'), 'url' => route('admin.events.index')],
            ['name' => __('New Event')],
        ];

        return view('backend.pages.events.create', compact('breadcrumbs'));
    }

    public function store(Request $request)
    {
        // Authorization
        $this->authorize('create', Event::class);

        // Validation rules with hooks
        $rules = Hook::applyFilters(EventFilterHook::EVENT_STORE_VALIDATION_RULES, [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'nullable|after_or_equal:start_time',
            'event_type' => 'required|string',
            'image_url' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp',
            'attachments.*' => 'nullable|file',
        ]);

        $validated = $request->validate($rules);

        // Before hook
        Hook::doAction(EventActionHook::EVENT_CREATED_BEFORE, $validated);

        // Use transaction for safety
        $event = DB::transaction(function () use ($validated, $request) {
            // Create event
            $event = $this->eventService->createEvent($validated);

            // Featured image
            if ($request->hasFile('featured_image')) {
                $file = $request->file('featured_image');
                if ($file instanceof UploadedFile && $file->isValid()) {
                    $event->addMedia($file)->toMediaCollection('featured');
                }
            }

            // Multiple attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file instanceof UploadedFile && $file->isValid()) {
                        $event->addMedia($file)->toMediaCollection('attachments');
                    }
                }
            }

            return $event;
        });

        // After hook
        Hook::doAction(EventActionHook::EVENT_CREATED_AFTER, $event);

        return redirect()
            ->route('admin.events.index')
            ->with('success', __('Event created successfully.'));
    }


    public function edit(Event $event): Renderable
    {
        $this->authorize('update', $event);

        $breadcrumbs = [
            ['name' => __('Events'), 'url' => route('admin.events.index')],
            ['name' => __('Edit Event')],
        ];

        return view('backend.pages.events.edit', compact('event', 'breadcrumbs'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $rules = Hook::applyFilters(EventFilterHook::EVENT_UPDATE_VALIDATION_RULES, [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'nullable|after_or_equal:start_time',
            'event_type' => 'required|string',
            'image_url' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp',
            'attachments.*' => 'nullable|file',
            'status' => 'string',
        ]);

        $validated = $request->validate($rules);

        Hook::doAction(EventActionHook::EVENT_UPDATED_BEFORE, $event, $validated);

        DB::transaction(function () use ($event, $validated) {
            $event = $this->eventService->updateEvent($event, $validated);
        });

        Hook::doAction(EventActionHook::EVENT_UPDATED_AFTER, $event);

        return redirect()
            ->route('admin.events.index')
            ->with('success', __('Event updated successfully.'));
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        Hook::doAction(EventActionHook::EVENT_DELETED_BEFORE, $event);

        $this->eventService->deleteEvent($event);

        Hook::doAction(EventActionHook::EVENT_DELETED_AFTER, $event);

        return redirect()
            ->route('admin.events.index')
            ->with('success', __('Event deleted successfully.'));
    }

    public function show(string $id): Renderable
    {
        $event = Event::with(['createdBy'])->findOrFail($id);

        $this->authorize('view', $event);

        $breadcrumbs = [
                ['name' => __('Events'), 'url' => route('admin.events.index')],
                ['name' => __('Edit Event')],
            ];
        
        $this->setBreadcrumbTitle(__('View :eventName', ['eventName' => $event->title]))
            ->addBreadcrumbItem(__('Events'), route('admin.events.index'));

        // Render using the helper that injects breadcrumbs into the layout (same pattern as posts)
        return $this->renderViewWithBreadcrumbs('backend.pages.events.show', compact('event', 'breadcrumbs'));
    }


   

    // event bulk delete
    public function bulkDelete(BulkDeleteRequest $request): RedirectResponse
    {
        $this->authorize('bulkDelete', Event::class);

        $ids = $request->validated('ids');

        if (empty($ids)) {
            session()->flash('error', __('No events selected for deletion.'));
            return redirect()->route('admin.events.index');
        }

        $ids = $this->addHooks(
            $ids,
            EventActionHook::EVENT_BULK_DELETED_BEFORE
        );

        $deletedCount = $this->eventService->bulkDeleteEvents($ids);

        $this->addHooks(
            ['deleted_count' => $deletedCount, 'post_type' => 'event'],
        EventActionHook::EVENT_BULK_DELETED_AFTER
        );

        if ($deletedCount > 0) {
            session()->flash('success', __(':count posts deleted successfully', ['count' => $deletedCount]));
        } else {
            session()->flash('error', __('No posts were deleted.'));
        }

        return redirect()->route('admin.events.index');
    }
}
