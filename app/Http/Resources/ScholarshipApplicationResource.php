<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScholarshipApplicationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'scholarship_id' => $this->scholarship_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'nationality' => $this->nationality,
            'country_of_residence' => $this->country_of_residence,
            'address' => $this->address,
            'current_education_level' => $this->current_education_level,
            'institution_name' => $this->institution_name,
            'field_of_study' => $this->field_of_study,
            'gpa' => $this->gpa,
            'graduation_year' => $this->graduation_year,
            'academic_achievements' => $this->academic_achievements,
            'research_area' => $this->research_area,
            'concept_note' => $this->concept_note,
            'motivation_letter' => $this->motivation_letter,
            'career_goals' => $this->career_goals,
            'why_this_scholarship' => $this->why_this_scholarship,
            'financial_need_description' => $this->financial_need_description,
            'reference_1_name' => $this->reference_1_name,
            'reference_1_email' => $this->reference_1_email,
            'reference_2_name' => $this->reference_2_name,
            'reference_2_email' => $this->reference_2_email,
            'additional_info' => $this->additional_info,
            'status' => $this->status,
            'submitted_at' => $this->submitted_at?->format('Y-m-d H:i:s'),
            'reviewed_at' => $this->reviewed_at?->format('Y-m-d H:i:s'),
            'decision_at' => $this->decision_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
