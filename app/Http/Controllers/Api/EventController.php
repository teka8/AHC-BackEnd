<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * List published events (no auth required).
     */
    public function index(Request $request)
    {
        $perPage = (int) ($request->input('per_page') ?? 10);
        $search = $request->input('search');

        // Filters
        $eventDate = $request->input('event_date'); // specific date
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $targetAudience = $request->input('target_audience');
        $category = $request->input('category');

        // Cost filters
        $costFilter = $request->input('cost_filter'); // free | lte | between | gte
        $costValue = $request->input('cost_value');
        $costFrom = $request->input('cost_from');
        $costTo = $request->input('cost_to');

        $query = Event::query()->whereIn('status', ['published', 'cancelled']);

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Specific event date
        if ($eventDate) {
            $query->whereDate('event_date', $eventDate);
        }

        // Date range (from - to)
        if ($fromDate && $toDate) {
            $query->whereBetween('event_date', [$fromDate, $toDate]);
        }

        // Filter by target audience
        if ($targetAudience) {
            $query->where('target_audience', $targetAudience);
        }

        // Filter by category
        if ($category) {
            $query->where('category', $category);
        }

        // Cost filter
        if ($costFilter) {
            switch ($costFilter) {
                case 'free':
                    $query->where('cost_amount', 0);
                    break;

                case 'lte':
                    if ($costValue !== null) {
                        $query->where('cost_amount', '<=', $costValue);
                    }
                    break;

                case 'gte':
                    if ($costValue !== null) {
                        $query->where('cost_amount', '>=', $costValue);
                    }
                    break;

                case 'between':
                    if ($costFrom !== null && $costTo !== null) {
                        $query->whereBetween('cost_amount', [$costFrom, $costTo]);
                    }
                    break;
            }
        }

        $events = $query->orderBy('event_date', 'desc')->paginate($perPage);

        return EventResource::collection($events);
    }

    public function show($id)
    {
        // Fetch the published event or fail
        $event = Event::whereIn('status', ['published', 'cancelled'])->findOrFail($id);

        // Return it using the EventResource
        return new EventResource($event);
    }
}
