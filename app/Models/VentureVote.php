<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentureVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'venture_id',
        'user_id',
        'ip_address',
    ];

    /**
     * Relationships
     */
    public function venture()
    {
        return $this->belongsTo(Venture::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
