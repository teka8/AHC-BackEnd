<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationalResourceTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    /**
     * Get the resources for the tag
     */
    public function resources()
    {
        return $this->belongsToMany(EducationalResource::class, 'educational_resource_tag');
    }
}