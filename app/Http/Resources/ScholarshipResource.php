<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class ScholarshipResource extends JsonResource
{
    public function toArray($request)
    {
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
            'deadline' => $this->deadline->format('Y-m-d'),
            'application_start_date' => $this->application_start_date?->format('Y-m-d'),
            'status' => $this->status,
            'available_slots' => $this->available_slots,
            'scholarship_image' => URL::asset($this->scholarship_image),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
