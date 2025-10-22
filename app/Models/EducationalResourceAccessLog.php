<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationalResourceAccessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'educational_resource_id',
        'user_id',
        'ip_address',
        'user_agent',
        'action',
        'time_spent_seconds',
        'completion_percentage',
        'referrer',
    ];

    protected $casts = [
        'time_spent_seconds' => 'integer',
        'completion_percentage' => 'decimal:2',
    ];

    /**
     * Get the resource that owns the access log
     */
    public function resource()
    {
        return $this->belongsTo(EducationalResource::class);
    }

    /**
     * Get the user that owns the access log
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}