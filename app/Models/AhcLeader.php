<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AhcLeader extends Model
{
    protected $fillable = [
        'type',
        'name',
        'position',
        'image',
        'description',
        'linkedin_url',
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
            $publicStoragePath = public_path('storage');
            if (is_link($publicStoragePath) || is_dir($publicStoragePath)) {
                return Storage::disk('public')->url($this->image);
            }

            $relative = ltrim(preg_replace('/^ahc-leaders\//', '', $this->image), '/');

            return route('media.ahc-leaders', ['path' => $relative]);
        }

        if (str_starts_with($this->image, '/')) {
            return $this->image;
        }

        return '/' . ltrim($this->image, '/');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeLeaders($query)
    {
        return $query->where('type', 'leader');
    }

    public function scopeTeam($query)
    {
        return $query->where('type', 'team');
    }
}
