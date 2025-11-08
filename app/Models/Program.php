<?php

namespace App\Models;

use App\Enums\ProgramStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
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

    protected $casts = [
        'state' => ProgramStatus::class,
    ];

    public function getImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('programs');
    }

    public function hasImage(): bool
    {
        return $this->hasMedia('programs');
    }

    public static function getProgramStatuses(): array
    {
        return collect(ProgramStatus::cases())
            ->mapWithKeys(fn ($case) => [$case->value => Str::of($case->name)->title()])
            ->toArray();
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
                    'icon' => 'lucide:check-circle',
                    'required_permission' => 'program.edit'
                ],
                'archive' => [
                    'target' => ProgramStatus::ARCHIVED->value,
                    'label' => __('Archive'),
                    'color' => 'gray',
                    'icon' => 'lucide:archive',
                    'required_permission' => 'program.edit'
                ]
            ],
            
            ProgramStatus::ACTIVE->value => [
                'pause' => [
                    'target' => ProgramStatus::PAUSED->value,
                    'label' => __('Pause'),
                    'color' => 'yellow',
                    'icon' => 'lucide:pause-circle',
                    'required_permission' => 'program.edit'
                ],
                'archive' => [
                    'target' => ProgramStatus::ARCHIVED->value,
                    'label' => __('Archive'),
                    'color' => 'gray',
                    'icon' => 'lucide:archive',
                    'required_permission' => 'program.edit'
                ]
            ],
            
            ProgramStatus::PAUSED->value => [
                'activate' => [
                    'target' => ProgramStatus::ACTIVE->value,
                    'label' => __('Activate'),
                    'color' => 'green',
                    'icon' => 'lucide:check-circle',
                    'required_permission' => 'program.edit'
                ],
                'archive' => [
                    'target' => ProgramStatus::ARCHIVED->value,
                    'label' => __('Archive'),
                    'color' => 'gray',
                    'icon' => 'lucide:archive',
                    'required_permission' => 'program.edit'
                ]
            ],
            
            ProgramStatus::ARCHIVED->value => [
                'restore' => [
                    'target' => ProgramStatus::UPCOMING->value,
                    'label' => __('Restore to Upcoming'),
                    'color' => 'blue',
                    'icon' => 'lucide:refresh-cw',
                    'required_permission' => 'program.edit'
                ]
            ]
        ];

        return $transitions[$currentState] ?? [];
    }

    /**
     * Get available actions for current user based on permissions
     */
    public function getAvailableActions(User $user = null): array
    {
        $user = $user ?: auth()->user();
        if (!$user) {
            return [];
        }

        $transitions = self::getAvailableTransitions($this->state->value, $user);
        $availableActions = [];

        foreach ($transitions as $action => $config) {
            if ($user->hasPermissionTo($config['required_permission']) || $user->hasRole('Superadmin')) {
                $availableActions[$action] = $config;
            }
        }

        return $availableActions;
    }

    /**
     * Check if user can perform specific action
     */
    public function canPerformAction(string $action, User $user = null): bool
    {
        $user = $user ?: auth()->user();
        if (!$user) {
            return false;
        }
        $availableActions = $this->getAvailableActions($user);
        
        return isset($availableActions[$action]);
    }

    /**
     * Change the status of the program.
     */
    public function changeStatus(string $action, User $user = null): bool
    {
        if (!$this->canPerformAction($action, $user)) {
            return false;
        }

        $transitions = self::getAvailableTransitions($this->state->value);
        $targetStatus = $transitions[$action]['target'] ?? null;

        if ($targetStatus) {
            $this->state = $targetStatus;
            return $this->save();
        }

        return false;
    }
}
