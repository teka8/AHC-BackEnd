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
}
