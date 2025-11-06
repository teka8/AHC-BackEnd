<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentureUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'venture_id',
        'title',
        'content',
        'update_type',
        'media',
        'likes_count',
        'comments_count',
    ];

    protected $casts = [
        'media' => 'array',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
    ];

    /**
     * Relationships
     */
    public function venture()
    {
        return $this->belongsTo(Venture::class);
    }

    /**
     * Scopes
     */
    public function scopeByVenture($query, $ventureId)
    {
        if ($ventureId) {
            return $query->where('venture_id', $ventureId);
        }
        return $query;
    }

    public function scopeByType($query, $type)
    {
        if ($type) {
            return $query->where('update_type', $type);
        }
        return $query;
    }
}
