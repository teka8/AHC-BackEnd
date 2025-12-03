<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class AhcLeader extends Model
{
    protected $fillable = [
        'name',
        'position',
        'image',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        if (Storage::disk('public')->exists($this->image)) {
            return URL::to(Storage::disk('public')->url($this->image));
        }

        if (str_starts_with($this->image, '/')) {
            return URL::to($this->image);
        }

        return URL::to('/' . ltrim($this->image, '/'));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
