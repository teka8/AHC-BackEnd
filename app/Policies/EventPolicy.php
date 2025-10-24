<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any events.
     */
    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'event.view');
    }

    /**
     * Determine whether the user can view a specific event.
     */
    public function view(User $user, Event $event): bool
    {
        // Check if user has general view permission
        if ($this->checkPermission($user, 'event.view')) {
            return true;
        }

        // Check if user can view their own event
        if ($this->checkPermission($user, 'event.view_own') && $this->userOwnsResource($user, $event)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create events.
     */
    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'event.create');
    }

    /**
     * Determine whether the user can update an event.
     */
    public function update(User $user, Event $event): bool
    {
        // Check if user has general edit permission
        if ($this->checkPermission($user, 'event.update')) {
            return true;
        }

        // Check if user can edit their own event
        if ($this->checkPermission($user, 'event.edit_own') && $this->userOwnsResource($user, $event)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete an event.
     */
    public function delete(User $user, Event $event): bool
    {
        // Check if user has general delete permission
        if ($this->checkPermission($user, 'event.delete')) {
            return true;
        }

        // Check if user can delete their own event
        if ($this->checkPermission($user, 'event.delete_own') && $this->userOwnsResource($user, $event)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore a deleted event.
     */
    public function restore(User $user, Event $event): bool
    {
        return $this->checkPermission($user, 'event.restore');
    }

    /**
     * Determine whether the user can permanently delete the event.
     */
    public function forceDelete(User $user, Event $event): bool
    {
        return $this->checkPermission($user, 'event.force_delete');
    }

    /**
     * Determine whether the user can bulk delete events.
     */
    public function bulkDelete(User $user): bool
    {
        return $this->checkPermission($user, 'event.delete');
    }

    /**
     * Determine whether the user can approve or publish events.
     */
    public function approve(User $user, Event $event): bool
    {
        return $this->checkPermission($user, 'event.approve');
    }
}
