<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Enums\Hooks\EventActionHook;
use App\Enums\Hooks\EventFilterHook;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BulkDeleteRequest;
use App\Models\Event;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Support\Facades\Hook;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\EventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Services\MediaLibraryService;
use Illuminate\Support\Facades\Storage;



class EventController extends Controller
{
    public function __construct(
        private readonly EventService $eventService,
        private readonly MediaLibraryService $mediaService
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

    public function store(StoreEventRequest $storeEventRequest): RedirectResponse{
        $this->authorize('create', Event::class);

        $data = $this->addHooks(
            $storeEventRequest->validated(),
            EventActionHook::EVENT_CREATED_BEFORE,
            EventFilterHook::EVENT_CREATED_BEFORE
        );

        // Create Event
        $event = new Event();
        $event->title = $data['title'];
        $event->description = $data['description'];
        $event->event_date = $data['event_date'];
        $event->start_time = date('H:i:s', strtotime($data['start_time']));
        $event->end_time = !empty($data['end_time']) ? date('H:i:s', strtotime($data['end_time'])) : null;
        $event->event_type = $data['event_type'];
        $event->category = $data['category'];
        $event->google_map_location_link = $data['google_map_location_link'];
        $event->registration_link = $data['registration_link'];
        $event->location = $data['location'];
        $event->register_on_site = $data['register_on_site'] ?? 0;
        $event->cost_amount = $data['cost_amount'] ?? 0;
        $event->target_audience = $data['target_audience'];
        $event->created_by = Auth::id();

        /**
         *  Handle Event Image Upload (Single)
         */
        if ($storeEventRequest->hasFile('event_image')) {
            $imagePath = $storeEventRequest->file('event_image')->store('events/images', 'public');
            $event->event_image = $imagePath;
        }

        /**
         *  Handle Multiple Attachments Upload
         */
        if ($storeEventRequest->hasFile('attachments')) {
            $attachments = [];

            foreach ($storeEventRequest->file('attachments') as $file) {
                $path = $file->store('events/attachments', 'public');

                $attachments[] = [
                    'file_name' => $file->getClientOriginalName(),
                    'size' => round($file->getSize() / 1048576, 2) . ' MB', // Convert bytes to MB
                    'path' => $path,
                ];
            }

            // Save as JSON in the database
            $event->attachments = $attachments;
        }


        $event->save();
        

        // Handle featured image removal first.
        if (isset($data['remove_featured_image']) && $data['remove_featured_image']) {
            $event->clearMediaCollection('featured');
        } elseif (! empty($data['event_image'])) {
            if ($storeEventRequest->hasFile('event_image')) {
                $event->clearMediaCollection('featured');
                $event->addMediaFromRequest('event_image')->toMediaCollection('featured');
            } else {
                $this->mediaService->associateExistingMedia($event, $data['event_image'], 'featured');
            }
        }

        // Handle attachments removal first.
        if (isset($data['remove_attachments']) && $data['remove_attachments']) {
            $event->clearMediaCollection('attachments');
        } elseif (! empty($data['attachments'])) {
            if ($storeEventRequest->hasFile('attachments')) {
                $event->clearMediaCollection('attachments');
                foreach ($storeEventRequest->file('attachments') as $file) {
                    $event->addMedia($file)->toMediaCollection('attachments');
                }
            } else {
                $this->mediaService->associateExistingMedia($event, $data['attachments'], 'attachments');
            }
        }


        
        $event = $this->addHooks(
            $event,
            EventActionHook::EVENT_CREATED_AFTER,
            EventFilterHook::EVENT_CREATED_AFTER
        );

        return redirect()->route('admin.events.index')->with('success', __('Event created successfully.'));

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



    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);

        $data = $this->addHooks(
            $request->validated(),
            EventActionHook::EVENT_UPDATED_BEFORE,
            EventFilterHook::EVENT_UPDATED_BEFORE
        );

        // Update core event fields
        $event->title = $data['title'];
        $event->description = $data['description'];
        $event->event_date = $data['event_date'];
        $event->start_time = date('H:i:s', strtotime($data['start_time']));
        $event->end_time = !empty($data['end_time']) ? date('H:i:s', strtotime($data['end_time'])) : null;
        $event->event_type = $data['event_type'];
        $event->category = $data['category'];
        $event->google_map_location_link = $data['google_map_location_link'];
        $event->registration_link = $data['registration_link'];
        $event->location = $data['location'];
        $event->register_on_site = $data['register_on_site'] ?? 0;
        $event->cost_amount = $data['cost_amount'] ?? 0;
        $event->target_audience = $data['target_audience'];
        $event->status = $data['status'];

        /**
         * Handle Event Image Update
         */
        if ($request->hasFile('event_image')) {
            // Delete old image if exists
            if ($event->event_image && Storage::disk('public')->exists($event->event_image)) {
                Storage::disk('public')->delete($event->event_image);
            }

            $imagePath = $request->file('event_image')->store('events/images', 'public');
            $event->event_image = $imagePath;
        }

        /**
         * Handle Attachments Update (Add or Remove)
         */
        $existingAttachments = is_array($event->attachments) ? $event->attachments : [];

        // Handle file removal if specified
        if ($request->filled('remove_attachments')) {
            $toRemove = $request->input('remove_attachments'); // array of file paths or indexes
            $existingAttachments = array_filter($existingAttachments, function ($attachment) use ($toRemove) {
                return !in_array($attachment['path'], $toRemove);
            });
            foreach ($toRemove as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }

        // Handle new file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('events/attachments', 'public');
                $existingAttachments[] = [
                    'file_name' => $file->getClientOriginalName(),
                    'size' => round($file->getSize() / 1048576, 2) . ' MB',
                    'path' => $path,
                ];
            }
        }

        $event->attachments = array_values($existingAttachments); // reindex

        $event->save();

        // Handle Media (if using Spatie media library)
        if (isset($data['remove_featured_image']) && $data['remove_featured_image']) {
            $event->clearMediaCollection('featured');
        } elseif ($request->hasFile('event_image')) {
            $event->clearMediaCollection('featured');
            $event->addMediaFromRequest('event_image')->toMediaCollection('featured');
        }

        if ($request->hasFile('attachments')) {
            $event->clearMediaCollection('attachments');
            foreach ($request->file('attachments') as $file) {
                $event->addMedia($file)->toMediaCollection('attachments');
            }
        }

        $event = $this->addHooks(
            $event,
            EventActionHook::EVENT_UPDATED_AFTER,
            EventFilterHook::EVENT_UPDATED_AFTER
        );

        return redirect()->route('admin.events.index')
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
