<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class AnalyticsPolicy
{
    /**
     * Determine if the user can view analytics dashboard.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_frontend_analytics');
    }
}
