<?php

namespace App\Models;

use App\Enums\ProgramStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\User;

class Program extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'host',
        'description',
        'image',
        'state',
    ];

    // Ensure new programs default to UPCOMING if state is omitted
    protected $attributes = [
        'state' => ProgramStatus::UPCOMING->value,
    ];

    protected $casts = [
        'state' => ProgramStatus::class,
    ];

    /**
     * Get the URL of the program's image (mirrors posts' featured image behavior).
     *
     * @param string|null $conversion optional conversion name (e.g. 'thumb')
     * @return string|null
     */
    public function getImageUrl(?string $conversion = null): ?string
    {
        // 1) Prefer the Media model URL (conversion if requested)
        $media = $this->getFirstMedia('featured');
        if ($media) {
            return $conversion ? $media->getUrl($conversion) : $media->getUrl();
        }

        // 2) Fallback to helper that may return converted url
        $url = $this->getFirstMediaUrl('featured', $conversion ?: '');
        if (!empty($url)) {
            return $url;
        }

        // 3) Fallback to image attribute (legacy column)
        if (!empty($this->image)) {
            return asset($this->image);
        }

        // 4) Final fallback: placeholder
        return asset('images/placeholder.png');
    }

    /**
     * Check if the program has a featured image
     */
    public function hasImage(): bool
    {
        return $this->hasMedia('featured') || !empty($this->image);
    }

    /**
     * Get all available program statuses
     */
    public static function getProgramStatuses(): array
    {
        return collect(ProgramStatus::cases())
            ->mapWithKeys(fn($case) => [$case->value => Str::of($case->name)->title()])
            ->toArray();
    }

    /**
     * Get available actions for the program based on its current state
     */
    public function getAvailableActions(): array
    {
        $actions = [];

        if ($this->state === ProgramStatus::UPCOMING) {
            $actions['activate'] = [
                'label' => __('Activate'),
                'icon' => 'lucide:check',
                'color' => 'green',
                'needs_comment' => false,
                'message' => __('This will activate the program and make it visible to users.'),
                'programId' => $this->id
            ];
        }

        if ($this->state === ProgramStatus::ACTIVE) {
            $actions['pause'] = [
                'label' => __('Pause'),
                'icon' => 'lucide:pause',
                'color' => 'yellow',
                'needs_comment' => false,
                'message' => __('This will pause the program and hide it from users.'),
                'programId' => $this->id
            ];

            $actions['archive'] = [
                'label' => __('Archive'),
                'icon' => 'lucide:archive',
                'color' => 'gray',
                'needs_comment' => false,
                'message' => __('This will archive the program.'),
                'programId' => $this->id
            ];
        }

        if ($this->state === ProgramStatus::PAUSED) {
            $actions['activate'] = [
                'label' => __('Activate'),
                'icon' => 'lucide:play',
                'color' => 'green',
                'needs_comment' => false,
                'message' => __('This will reactivate the program.'),
                'programId' => $this->id
            ];

            $actions['archive'] = [
                'label' => __('Archive'),
                'icon' => 'lucide:archive',
                'color' => 'gray',
                'needs_comment' => false,
                'message' => __('This will archive the program.'),
                'programId' => $this->id
            ];
        }

        if ($this->state === ProgramStatus::ARCHIVED) {
            $actions['restore'] = [
                'label' => __('Restore'),
                'icon' => 'lucide:refresh-cw',
                'color' => 'blue',
                'needs_comment' => false,
                'message' => __('This will restore the program to the upcoming state.'),
                'programId' => $this->id
            ];
        }

        return $actions;
    }

    /**
     * Change the program status
     */
    public function changeStatus(string $action, ?User $user = null, string $comment = ''): bool
    {
        $user = $user ?: auth()->user();

        if (!$user || !$this->canPerformAction($action, $user)) {
            return false;
        }

        $newStatus = $this->getStatusFromAction($action);

        if (!$newStatus) {
            return false;
        }

        $this->state = $newStatus;
        $this->save();

        // Log the status change
        activity()
            ->causedBy($user)
            ->performedOn($this)
            ->withProperties([
                'action' => $action,
                'old_status' => $this->getOriginal('state'),
                'new_status' => $newStatus,
                'comment' => $comment,
            ])
            ->log("Program status changed to {$newStatus->value}");

        return true;
    }

    /**
     * Get the new status based on the action
     */
    protected function getStatusFromAction(string $action): ?ProgramStatus
    {
        return match ($action) {
            'activate' => ProgramStatus::ACTIVE,
            'pause' => ProgramStatus::PAUSED,
            'archive' => ProgramStatus::ARCHIVED,
            'restore' => ProgramStatus::UPCOMING,
            default => null,
        };
    }

    /**
     * Check if a user can perform an action on the program
     */
    public function canPerformAction(string $action, ?User $user = null): bool
    {
        $user = $user ?: auth()->user();

        if (!$user) {
            return false;
        }

        // Superadmin can do anything
        if ($user->hasRole('Superadmin')) {
            return true;
        }

        // Check specific permissions
        return match ($action) {
            'activate', 'pause' => $user->can('program.publish', $this),
            'archive', 'restore' => $user->can('program.edit', $this),
            default => false,
        };
    }

    /**
     * Available transitions for programs with permission requirements
     */
    public static function getAvailableTransitions(string $currentState, User $user = null): array
    {
        $transitions = [
            ProgramStatus::UPCOMING->value => [
                'activate' => [
                    'target' => ProgramStatus::ACTIVE->value,
                    'label' => __('Activate'),
                    'color' => 'green',
                ],
            ],
            ProgramStatus::ACTIVE->value => [
                'pause' => [
                    'target' => ProgramStatus::PAUSED->value,
                    'label' => __('Pause'),
                    'color' => 'yellow',
                ],
                'archive' => [
                    'target' => ProgramStatus::ARCHIVED->value,
                    'label' => __('Archive'),
                    'color' => 'gray',
                ],
            ],
            ProgramStatus::PAUSED->value => [
                'activate' => [
                    'target' => ProgramStatus::ACTIVE->value,
                    'label' => __('Activate'),
                    'color' => 'green',
                ],
                'archive' => [
                    'target' => ProgramStatus::ARCHIVED->value,
                    'label' => __('Archive'),
                    'color' => 'gray',
                ],
            ],
            ProgramStatus::ARCHIVED->value => [
                'restore' => [
                    'target' => ProgramStatus::UPCOMING->value,
                    'label' => __('Restore'),
                    'color' => 'blue',
                ],
            ],
        ];

        return $transitions[$currentState] ?? [];
    }

    /**
     * Register the featured media collection (mirror posts).
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured')
            ->singleFile()
            ->useDisk('public');
    }

    /**
     * Register conversions (e.g., thumbnail)
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(250)
            ->sharpen(10)
            ->nonQueued(); // remove nonQueued() if you process conversions via queue
    }

    /**
     * Ensure empty or invalid state values from forms do not overwrite the current state.
     * This allows the edit form to omit or send an empty state input (i.e. remove `required` from the select)
     * without clearing or setting an invalid enum value on the model.
     */
        public function setStateAttribute($value): void
        {
            // Ignore null or empty string (keeps existing state)
            if ($value === null || $value === '') {
                return;
            }
    
            // If already an enum instance, set its value
            if ($value instanceof ProgramStatus) {
                $this->attributes['state'] = $value->value;
                return;
            }
    
            // Try to coerce a valid enum value from string; ignore invalid values
            try {
                $enum = ProgramStatus::from($value);
                $this->attributes['state'] = $enum->value;
            } catch (\ValueError $e) {
                // Invalid state provided — ignore to avoid breaking the model
                return;
                    }
                }
            }