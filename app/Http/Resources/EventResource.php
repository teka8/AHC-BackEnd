<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'event_date' => $this->event_date,
            'location' => $this->location,
            'google_map_location_link' => $this->google_map_location_link,
            'category' => $this->category,
            'register_on_site' => (bool) $this->register_on_site,
            'registration_link' => $this->registration_link,
            'cost_amount' => $this->cost_amount,
            'event_type' => $this->event_type,
            'target_audience' => $this->target_audience,
            'status' => $this->status,
            'image_url' => $this->image_url,
            'is_archived' => (bool) $this->is_archived,
            'created_by' => $this->created_by,
            'approved_by' => $this->approved_by,
            'reviewed_by' => $this->reviewed_by,
            'archived_by' => $this->archived_by,
            'attachments' => is_array($this->attachments) ? $this->attachments : json_decode($this->attachments ?? '[]', true),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
