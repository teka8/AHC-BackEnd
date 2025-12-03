<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AhcLeader;
use App\Models\User;

class AhcLeaderPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'ahc-leader.view');
    }

    public function view(User $user, AhcLeader $ahcLeader): bool
    {
        return $this->checkPermission($user, 'ahc-leader.view');
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'ahc-leader.create');
    }

    public function update(User $user, AhcLeader $ahcLeader): bool
    {
        return $this->checkPermission($user, 'ahc-leader.update');
    }

    public function delete(User $user, AhcLeader $ahcLeader): bool
    {
        return $this->checkPermission($user, 'ahc-leader.delete');
    }

    public function bulkDelete(User $user): bool
    {
        return $this->checkPermission($user, 'ahc-leader.delete');
    }
}
