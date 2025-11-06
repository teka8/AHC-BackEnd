<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;
use App\Concerns\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;

class Venture extends Model implements SpatieHasMedia
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'name',
        'tagline',
        'description',
        'focus_area',
        'stage',
        'founded_year',
        'country',
        'website',
        'social_links',
        'founders',
        'team_size',
        'funding_raised',
        'patients_impacted',
        'countries_reached',
        'logo',
        'pitch_video',
        'demo_video',
        'images',
        'votes_count',
        'featured',
        'status',
    ];

    protected $casts = [
        'social_links' => 'array',
        'images' => 'array',
        'votes_count' => 'integer',
        'featured' => 'boolean',
        'founded_year' => 'integer',
        'team_size' => 'integer',
        'patients_impacted' => 'integer',
        'countries_reached' => 'integer',
        'funding_raised' => 'decimal:2',
    ];

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

        $this->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    /**
     * Define media conversions
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Contain, 200, 200)
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->fit(Fit::Contain, 800, 600)
            ->nonQueued();
    }

    /**
     * Relationships
     */
    public function updates()
    {
        return $this->hasMany(VentureUpdate::class);
    }

    public function votes()
    {
        return $this->hasMany(VentureVote::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeByFocusArea($query, $focusArea)
    {
        if ($focusArea && $focusArea !== 'all') {
            return $query->where('focus_area', $focusArea);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('tagline', 'like', "%{$search}%");
            });
        }
        return $query;
    }
}
