<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Page;
use App\Models\User;

class PagePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'news.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Page $page): bool
    {
        // Check if user has general view permission
        if ($this->checkPermission($user, 'news.view')) {
            return true;
        }

        // Check if user can view their own posts
        if ($this->checkPermission($user, 'news.view_own') && $this->userOwnsResource($user, $page)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'news.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Page $page): bool
    {
        // Check if user has general edit permission
        if ($this->checkPermission($user, 'news.edit')) {
            return true;
        }

        // Check if user can edit their own posts
        if ($this->checkPermission($user, 'news.edit_own') && $this->userOwnsResource($user, $page)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Page $page): bool
    {
        // Check if user has general delete permission
        if ($this->checkPermission($user, 'news.delete')) {
            return true;
        }

        // Check if user can delete their own posts
        if ($this->checkPermission($user, 'news.delete_own') && $this->userOwnsResource($user, $page)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Page $page): bool
    {
        return $this->checkPermission($user, 'news.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Page $page): bool
    {
        return $this->checkPermission($user, 'news.force_delete');
    }

    /**
     * Determine whether the user can bulk delete models.
     */
    public function bulkDelete(User $user): bool
    {
        return $this->checkPermission($user, 'news.delete');
    }

    /**
     * Determine whether the user can publish the post.
     */
    public function publish(User $user, Page $page): bool
    {
        return $this->checkPermission($user, 'news.publish');
    }

    /**
     * Determine whether the user can manage AI content.
     */
    public function aiContent(User $user): bool
    {
        return $this->checkPermission($user, 'ai_content.generate');
    }
}