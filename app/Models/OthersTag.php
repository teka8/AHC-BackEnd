<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OthersTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    /**
     * Get the documents for the tag
     */
    public function documents()
    {
        return $this->belongsToMany(Others::class, 'others_tag');
    }
}