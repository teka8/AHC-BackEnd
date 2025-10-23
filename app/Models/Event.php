<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;
use App\Concerns\HasMedia; // Reuse the same trait as in Post.php
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Event extends Model implements SpatieHasMedia
{
    use HasFactory;
    use HasMedia;

    protected $table = 'events';

    protected $fillable = [
        'title',
        'description',
        'start_time',
        'end_time',
        'event_date',
        'location',
        'google_map_location_link',
        'category',
        'register_on_site',
        'registration_link',
        'cost_amount',
        'event_type',
        'target_audience',
        'status',
        'event_image',
        'is_archived',
        'created_by',
        'approved_by',
        'reviewed_by',
        'published_by',
        'archived_by',
        'attachments',
    ];

    protected $casts = [
        'register_on_site' => 'boolean',
        'is_archived' => 'boolean',
        'event_date' => 'date',
        'attachments' => 'array',
    ];

    /**
     * Register media collections for events.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

        $this->addMediaCollection('attachments')
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',

                'application/pdf',
                'application/msword', // .doc
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
                'application/rtf',

                // Spreadsheets
                'application/vnd.ms-excel', // .xls
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                'text/csv',
                'text/plain',

                // Presentations
                'application/vnd.ms-powerpoint', // .ppt
                'application/vnd.openxmlformats-officedocument.presentationml.presentation', // .pptx

                // Archives
                'application/zip',
                'application/x-7z-compressed',
                'application/x-rar-compressed',
                'application/x-tar',
                'application/x-gzip',

                

            ]);
    }

    /**
     * Define media conversions for various uses.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Contain, 200, 200)
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->fit(Fit::Contain, 600, 400)
            ->nonQueued();

        $this->addMediaConversion('large')
            ->fit(Fit::Contain, 1200, 800)
            ->nonQueued();
    }

    /**
     * Get featured image URL or a default placeholder.
     */
    public function getFeaturedImageUrl(string $conversion = ''): ?string
    {
        $media = $this->getFirstMedia('featured');
        if (! $media) {
            return null;
        }

        return $conversion ? $media->getUrl($conversion) : $media->getUrl();
    }

    /**
     * Check if event has a featured image.
     */
    public function hasFeaturedImage(): bool
    {
        return $this->hasMedia('featured');
    }

    /**
     * Get all attached media files.
     */
    public function getAttachments(): array
    {
        return $this->getMedia('attachments')->map(function ($media) {
            return [
                'id' => $media->id,
                'name' => $media->name,
                'original' => $media->getUrl(),
                'thumb' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : null,
                'type' => $media->mime_type,
            ];
        })->toArray();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
