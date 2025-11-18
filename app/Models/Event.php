<?php

namespace App\Models;

use App\Concerns\HasMedia; // Reuse the same trait as in Post.php
use App\Observers\EventObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[ObservedBy([EventObserver::class])]
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
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Event workflow states
     */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_ARCHIVED = 'archived';

    /**
     * Available transitions for events with permission requirements
     */
    public static function getAvailableTransitions($currentStatus, User $user = null)
    {
        $transitions = [
            self::STATUS_DRAFT => [
                'send_for_review' => [
                    'target' => self::STATUS_UNDER_REVIEW,
                    'label' => __('Send for Review'),
                    'color' => 'yellow',
                    'icon' => 'lucide:send',
                    'required_permission' => 'event.review',
                ],
                'publish' => [
                    'target' => self::STATUS_PUBLISHED,
                    'label' => __('Publish Event'),
                    'color' => 'green',
                    'icon' => 'lucide:globe',
                    'required_permission' => 'event.publish',
                ],
            ],

            self::STATUS_UNDER_REVIEW => [
                'approve' => [
                    'target' => self::STATUS_APPROVED,
                    'label' => __('Approve Event'),
                    'color' => 'green',
                    'icon' => 'lucide:check-circle',
                    'required_permission' => 'event.approve',
                ],
                'reject' => [
                    'target' => self::STATUS_DRAFT,
                    'label' => __('Request Changes'),
                    'color' => 'red',
                    'icon' => 'lucide:arrow-left',
                    'required_permission' => 'event.reject',
                ],
            ],

            self::STATUS_APPROVED => [
                'publish' => [
                    'target' => self::STATUS_PUBLISHED,
                    'label' => __('Publish Event'),
                    'color' => 'green',
                    'icon' => 'lucide:globe',
                    'required_permission' => 'event.publish',
                ],
                'send_back' => [
                    'target' => self::STATUS_UNDER_REVIEW,
                    'label' => __('Send Back for Review'),
                    'color' => 'yellow',
                    'icon' => 'lucide:arrow-left',
                    'required_permission' => 'event.review',
                ],
            ],

            self::STATUS_PUBLISHED => [
                'complete' => [
                    'target' => self::STATUS_COMPLETED,
                    'label' => __('Mark as Completed'),
                    'color' => 'purple',
                    'icon' => 'lucide:check-square',
                    'required_permission' => 'event.complete',
                ],
                'cancel' => [
                    'target' => self::STATUS_CANCELLED,
                    'label' => __('Cancel Event'),
                    'color' => 'red',
                    'icon' => 'lucide:x-circle',
                    'required_permission' => 'event.cancel',
                ],
                'unpublish' => [
                    'target' => self::STATUS_DRAFT,
                    'label' => __('Unpublish'),
                    'color' => 'gray',
                    'icon' => 'lucide:eye-off',
                    'required_permission' => 'event.unpublish',
                ],
            ],

            self::STATUS_COMPLETED => [
                'archive' => [
                    'target' => self::STATUS_ARCHIVED,
                    'label' => __('Archive Event'),
                    'color' => 'orange',
                    'icon' => 'lucide:archive',
                    'required_permission' => 'event.archive',
                ],
                'reopen' => [
                    'target' => self::STATUS_PUBLISHED,
                    'label' => __('Reopen Event'),
                    'color' => 'blue',
                    'icon' => 'lucide:refresh-cw',
                    'required_permission' => 'event.publish',
                ],
            ],

            self::STATUS_CANCELLED => [
                'reopen' => [
                    'target' => self::STATUS_DRAFT,
                    'label' => __('Reopen Draft'),
                    'color' => 'blue',
                    'icon' => 'lucide:refresh-cw',
                    'required_permission' => 'event.publish',
                ],
                'archive' => [
                    'target' => self::STATUS_ARCHIVED,
                    'label' => __('Archive Event'),
                    'color' => 'orange',
                    'icon' => 'lucide:archive',
                    'required_permission' => 'event.archive',
                ],
            ],

            self::STATUS_ARCHIVED => [
                'restore' => [
                    'target' => self::STATUS_DRAFT,
                    'label' => __('Restore Event'),
                    'color' => 'blue',
                    'icon' => 'lucide:refresh-cw',
                    'required_permission' => 'event.restore',
                ],
            ],
        ];

        return $transitions[$currentStatus] ?? [];
    }

    /**
     * Get available actions for current user based on permissions
     */
    public function getAvailableActions(User $user = null)
    {
        $user = $user ?: auth()->user();
        $transitions = self::getAvailableTransitions($this->status, $user);
        $availableActions = [];

        foreach ($transitions as $action => $config) {
            if ($user->hasPermissionTo($config['required_permission']) || $user->hasRole('super_admin')) {
                $availableActions[$action] = $config;
            }
        }

        return $availableActions;
    }

    /**
     * Check if user can perform specific action
     */
    public function canPerformAction($action, User $user = null)
    {
        $user = $user ?: auth()->user();
        $availableActions = $this->getAvailableActions($user);

        return isset($availableActions[$action]);
    }

    /**
     * Get status badge color
     */
    public function getStatusColor()
    {
        $colors = [
            self::STATUS_DRAFT => 'gray',
            self::STATUS_UNDER_REVIEW => 'yellow',
            self::STATUS_APPROVED => 'blue',
            self::STATUS_PUBLISHED => 'green',
            self::STATUS_CANCELLED => 'red',
            self::STATUS_COMPLETED => 'purple',
            self::STATUS_ARCHIVED => 'orange',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Get status display text
     */
    public function getStatusDisplay()
    {
        $display = [
            self::STATUS_DRAFT => __('Draft'),
            self::STATUS_UNDER_REVIEW => __('Under Review'),
            self::STATUS_APPROVED => __('Approved'),
            self::STATUS_PUBLISHED => __('Published'),
            self::STATUS_CANCELLED => __('Cancelled'),
            self::STATUS_COMPLETED => __('Completed'),
            self::STATUS_ARCHIVED => __('Archived'),
        ];

        return $display[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }
}
