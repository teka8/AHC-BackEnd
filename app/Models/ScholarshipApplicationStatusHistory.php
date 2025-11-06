<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipApplicationStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'scholarship_application_status_history';

    public $timestamps = false;

    protected $fillable = [
        'application_id',
        'status',
        'note',
        'updated_by',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function application()
    {
        return $this->belongsTo(ScholarshipApplication::class, 'application_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
