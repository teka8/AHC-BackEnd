<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VentureResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'tagline' => $this->tagline,
            'description' => $this->description,
            'focus_area' => $this->focus_area,
            'stage' => $this->stage,
            'founded_year' => $this->founded_year,
            'country' => $this->country,
            'website' => $this->website,
            'social_links' => $this->social_links,
            'founders' => $this->founders,
            'team_size' => $this->team_size,
            'funding_raised' => $this->funding_raised,
            'patients_impacted' => $this->patients_impacted,
            'countries_reached' => $this->countries_reached,
            'logo' => $this->logo ? asset('storage/' . $this->logo) : null,
            'pitch_video' => $this->pitch_video,
            'demo_video' => $this->demo_video,
            'images' => $this->images,
            'votes_count' => $this->votes_count,
            'featured' => $this->featured,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
