<?php

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactMessagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('contact_message.view') || $user->hasRole('Superadmin');
    }

    public function view(User $user, ContactMessage $contactMessage): bool
    {
        return $user->can('contact_message.view') || $user->hasRole('Superadmin');
    }

    public function update(User $user, ContactMessage $contactMessage): bool
    {
        return $user->can('contact_message.view') || $user->hasRole('Superadmin');
    }

    public function delete(User $user, ContactMessage $contactMessage): bool
    {
        return $user->can('contact_message.delete') || $user->hasRole('Superadmin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('contact_message.delete') || $user->hasRole('Superadmin');
    }
}
