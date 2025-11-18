<?php

namespace App\Policies;

use App\Models\EmailSubscription;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmailSubscriptionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('subscription.view') || $user->hasRole('Superadmin');
    }

    public function view(User $user, EmailSubscription $subscription): bool
    {
        return $user->can('subscription.view') || $user->hasRole('Superadmin');
    }

    public function update(User $user, EmailSubscription $subscription): bool
    {
        return $user->can('subscription.update') || $user->hasRole('Superadmin');
    }

    public function delete(User $user, EmailSubscription $subscription): bool
    {
        return $user->can('subscription.delete') || $user->hasRole('Superadmin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('subscription.delete') || $user->hasRole('Superadmin');
    }
}
