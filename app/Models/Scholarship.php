<?php

namespace App\Models;

use App\Observers\ScholarshipObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([ScholarshipObserver::class])]
class Scholarship extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'program_type',
        'eligibility_criteria',
        'required_documents',
        'benefits',
        'coverage',
        'amount',
        'deadline',
        'application_start_date',
        'status',
        'available_slots',
        'scholarship_image',
    ];

    protected $casts = [
        'required_documents' => 'array',
        'benefits' => 'array',
        'deadline' => 'date',
        'application_start_date' => 'date',
        'amount' => 'decimal:2',
        'available_slots' => 'integer',
    ];

    /**
     * Relationships
     */
    public function applications()
    {
        return $this->hasMany(ScholarshipApplication::class);
    }

    /**
     * Scopes
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Determine if the scholarship deadline has passed (if set)
     */
    public function isPastDeadline(): bool
    {
        if (! $this->deadline) {
            return false;
        }

        return \Carbon\Carbon::parse($this->deadline)->endOfDay()->isPast();
    }

    /**
     * Determine if the scholarship application period has not started yet
     */
    public function isUpcoming(): bool
    {
        if ($this->status === 'upcoming') {
            return true;
        }

        if ($this->application_start_date && \Carbon\Carbon::parse($this->application_start_date)->startOfDay()->isFuture()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the scholarship is currently open and accepting applications
     */
    public function isOpen(): bool
    {
        if ($this->status !== 'open') {
            return false;
        }

        if ($this->isPastDeadline()) {
            return false;
        }

        if ($this->isUpcoming()) {
            return false;
        }

        return true;
    }
}

