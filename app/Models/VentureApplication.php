<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;
use App\Concerns\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class VentureApplication extends Model implements SpatieHasMedia
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'venture_name',
        'tagline',
        'description',
        'focus_area',
        'stage',
        'founded_year',
        'country',
        'website',
        'contact_name',
        'contact_email',
        'contact_phone',
        'founders',
        'team_size',
        'team_description',
        'problem_statement',
        'solution_description',
        'target_market',
        'unique_value_proposition',
        'current_stage_description',
        'patients_served',
        'revenue_generated',
        'funding_raised',
        'key_milestones',
        'funding_sought',
        'use_of_funds',
        'pitch_deck',
        'business_plan',
        'financial_projections',
        'why_apply',
        'additional_info',
        'status',
        'submitted_at',
        'user_id',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'founded_year' => 'integer',
        'team_size' => 'integer',
        'patients_served' => 'integer',
        'revenue_generated' => 'decimal:2',
        'funding_raised' => 'decimal:2',
        'funding_sought' => 'decimal:2',
    ];

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('pitch_deck')
            ->singleFile()
            ->acceptsMimeTypes([
                'application/pdf',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            ]);

        $this->addMediaCollection('business_plan')
            ->singleFile()
            ->acceptsMimeTypes([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]);

        $this->addMediaCollection('financial_projections')
            ->singleFile()
            ->acceptsMimeTypes([
                'application/pdf',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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
}
