<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'reviewer_id',
        'academic_performance_score',
        'motivation_score',
        'research_quality_score',
        'financial_need_score',
        'overall_score',
        'strengths',
        'weaknesses',
        'recommendation',
        'notes',
    ];

    protected $casts = [
        'academic_performance_score' => 'decimal:1',
        'motivation_score' => 'decimal:1',
        'research_quality_score' => 'decimal:1',
        'financial_need_score' => 'decimal:1',
        'overall_score' => 'decimal:1',
    ];

    /**
     * Relationships
     */
    public function application()
    {
        return $this->belongsTo(ScholarshipApplication::class, 'application_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
