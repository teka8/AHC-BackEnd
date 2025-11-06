<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;
use App\Concerns\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ScholarshipApplication extends Model implements SpatieHasMedia
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'scholarship_id',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'nationality',
        'country_of_residence',
        'address',
        'current_education_level',
        'institution_name',
        'field_of_study',
        'gpa',
        'graduation_year',
        'academic_achievements',
        'research_area',
        'concept_note',
        'research_proposal',
        'motivation_letter',
        'career_goals',
        'why_this_scholarship',
        'financial_need_description',
        'current_funding_sources',
        'reference_1_name',
        'reference_1_email',
        'reference_1_relationship',
        'reference_2_name',
        'reference_2_email',
        'reference_2_relationship',
        'cv',
        'transcript',
        'motivation_letter_file',
        'recommendation_letter_1',
        'recommendation_letter_2',
        'id_document',
        'proof_of_enrollment',
        'additional_info',
        'status',
        'submitted_at',
        'reviewed_at',
        'decision_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'graduation_year' => 'integer',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'decision_at' => 'datetime',
    ];

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cv')
            ->singleFile()
            ->acceptsMimeTypes([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]);

        $this->addMediaCollection('transcript')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);

        $this->addMediaCollection('motivation_letter_file')
            ->singleFile()
            ->acceptsMimeTypes([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]);

        $this->addMediaCollection('recommendation_letter_1')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);

        $this->addMediaCollection('recommendation_letter_2')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);

        $this->addMediaCollection('id_document')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);

        $this->addMediaCollection('proof_of_enrollment')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);
    }

    /**
     * Relationships
     */
    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function evaluations()
    {
        return $this->hasMany(ScholarshipEvaluation::class, 'application_id');
    }

    public function statusHistory()
    {
        return $this->hasMany(ScholarshipApplicationStatusHistory::class, 'application_id');
    }

    /**
     * Scopes
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSubmitted($query)
    {
        return $query->whereNotNull('submitted_at');
    }

    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }
}
