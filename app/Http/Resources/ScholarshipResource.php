<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ScholarshipResource extends JsonResource
{
    public function toArray($request)
    {
        $imagePath = $this->scholarship_image;
        $imageUrl = $imagePath ? Storage::disk('public')->url($imagePath) : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'program_type' => $this->program_type,
            'eligibility_criteria' => $this->eligibility_criteria,
            'required_documents' => $this->required_documents,
            'benefits' => $this->benefits,
            'coverage' => $this->coverage,
            'amount' => $this->amount,
            'deadline' => $this->deadline?->format('Y-m-d'),
            'application_start_date' => $this->application_start_date?->format('Y-m-d'),
            'status' => $this->status,
            'is_open' => $this->isOpen(),
            'is_past_deadline' => $this->isPastDeadline(),
            'available_slots' => $this->available_slots,
            'image_path' => $imagePath,
            'image_url' => $imageUrl,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
