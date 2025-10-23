<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Event;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use App\Enums\Hooks\EventFilterHook;
use App\Support\Facades\Hook;
use Illuminate\Support\Facades\DB;

class EventService
{
    /**
     * Get paginated events with filters.
     */
    public function getEvents(array $filters = []): LengthAwarePaginator
    {
        $query = Event::query()->with(['createdBy']);

        // Apply custom filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('event_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_time'])) {
            $query->whereTime('event_time', '<=', $filters['end_time']);
        }

        if (!empty($filters['start_time'])) {
            $query->whereTime('event_time', '>=', $filters['start_time']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    /**
     * Get single event by ID.
     */
    public function getEventById(?int $id): ?Event
    {
        if (empty($id)) {
            return null;
        }

        return Event::with(['createdBy'])->findOrFail($id);
    }

    /**
     * Create new event with media support.
     */


    public function createEvent(array $data): Event
    {
        // Use a transaction to ensure atomicity
        return DB::transaction(function () use ($data) {

            // Create the event safely using null coalescing for optional fields
            $event = Event::create([
                'title' => $data['title'], // required
                'description' => $data['description'] ?? null,
                'event_date' => $data['event_date'], // required
                'start_time' => date('H:i:s', strtotime($data['start_time'])), // required,
                'end_time' => !empty($data['end_time']) ? date('H:i:s', strtotime($data['end_time'])) : null,
                'location' => $data['location'] ?? null,
                'event_image' => $data['event_image'] ?? null,
                'google_map_location_link' => $data['google_map_location_link'] ?? null,
                'category' => $data['category'] ?? null,
                'register_on_site' => $data['register_on_site'] ?? false,
                'registration_link' => $data['registration_link'] ?? null,
                'cost_amount' => $data['cost_amount'] ?? null,
                'event_type' => $data['event_type'], // required
                'target_audience' => $data['target_audience'] ?? null,
                'attachments' => $data['attachments'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'is_archived' => $data['is_archived'] ?? false,
                'created_by' => $data['created_by'] ?? null,
                'approved_by' => $data['approved_by'] ?? null,
                'reviewed_by' => $data['reviewed_by'] ?? null,
                'archived_by' => $data['archived_by'] ?? null,
                'published_by' => $data['published_by'] ?? null,
                'attachments*' => $data['attachments'] ?? null,
            ]);

            // Handle featured image if provided
            if (!empty($data['event_image']) && $data['event_image'] instanceof UploadedFile) {
                $event->addMedia($data['event_image'])->toMediaCollection('featured');
            }

            // Handle attachments if provided
            if (!empty($data['attachments']) && is_array($data['attachments'])) {
                foreach ($data['attachments'] as $file) {
                    if ($file instanceof UploadedFile) {
                        $event->addMedia($file)->toMediaCollection('attachments');
                    }
                }
            }

            return $event;
        });
    }


    /**
     * Update existing event with media support.
     */
    public function updateEvent(Event $event, array $data): Event
    {
        
        $event->update([
            'title' => $data['title'] ?? $event->title,
            'description' => $data['description'] ?? $event->description,
            'event_date' => $data['event_date'] ?? $event->event_date,
            'start_time' => date('H:i:s', strtotime($data['start_time'] ?? $event->start_time)),
            'end_time' => !empty($data['end_time']) ? date('H:i:s', strtotime($data['end_time'] ?? $event->end_time)) : null,
            'location' => $data['location'] ?? $event->location,
            'event_image' => $data['event_image'] ?? $event->event_image,
            'google_map_location_link' => $data['google_map_location_link'] ?? $event->google_map_location_link,
            'category' => $data['category'] ?? $event->category,
            'register_on_site' => $data['register_on_site'] ?? $event->register_on_site,
            'registration_link' => $data['registration_link'] ?? $event->registration_link,
            'cost_amount' => $data['cost_amount'] ?? $event->cost_amount,
            'event_type' => $data['event_type'] ?? $event->event_type,
            'target_audience' => $data['target_audience'] ?? $event->target_audience,
            'attachments' => $data['attachments'] ?? $event->attachments,
            'status' => $data['status'] ?? $event->status,
            'is_archived' => $data['is_archived'] ?? $event->is_archived,
            'approved_by' => $data['approved_by'] ?? $event->approved_by,
            'reviewed_by' => $data['reviewed_by'] ?? $event->reviewed_by,
            'published_by' => $data['published_by'] ?? $event->published_by,
            'archived_by' => $data['archived_by'] ?? $event->archived_by,
        ]);

        // Handle featured image replacement
        if (!empty($data['event_image']) && $data['event_image'] instanceof UploadedFile) {
            $event->clearMediaCollection('featured');
            $event->addMedia($data['event_image'])->toMediaCollection('featured');
        }

        // Optionally remove featured image
        if (!empty($data['remove_featured_image']) && $data['remove_featured_image']) {
            $event->clearMediaCollection('featured');
        }

        // Handle attachments
        if (!empty($data['attachments'])) {
            foreach ($data['attachments'] as $file) {
                if ($file instanceof UploadedFile) {
                    $event->addMedia($file)->toMediaCollection('attachments');
                }
            }
        }
    

        return $event;
    }

    /**
     * Delete an event.
     */
    public function deleteEvent(Event $event): void
    {
        $event->delete();
    }

    /**
     * Bulk delete multiple events.
     */
    public function bulkDeleteEvents(array $ids): int
    {
        if (empty($ids)) {
            return 0;
        }

        $events = Event::whereIn('id', $ids)->get();
        $deletedCount = 0;

        foreach ($events as $event) {
            $this->deleteEvent($event);
            $deletedCount++;
        }

        return $deletedCount;
    }

    /**
     * Get event permalink (frontend URL).
     */
    public function getEventPermalink(Event|int|null $event): ?string
    {
        if (is_numeric($event)) {
            $event = $this->getEventById($event);
        }

        if (!$event) {
            return null;
        }

        return route('events.show', ['slug' => $event->slug ?? $event->id]);
    }

    /**
     * Get formatted event date.
     */
    public function getEventDate(Event|int|null $event, string $format = 'M d, Y'): ?string
    {
        if (is_numeric($event)) {
            $event = $this->getEventById($event);
        }

        if (!$event) {
            return null;
        }

        return $event->event_date?->format($format) ?? $event->created_at->format($format);
    }
}
