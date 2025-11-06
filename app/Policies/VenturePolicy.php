<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Venture;
use App\Models\User;

class VenturePolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'venture.view');
    }

    public function view(User $user, Venture $venture): bool
    {
        return $this->checkPermission($user, 'venture.view');
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'venture.create');
    }

    public function update(User $user, Venture $venture): bool
    {
        return $this->checkPermission($user, 'venture.update');
    }

    public function delete(User $user, Venture $venture): bool
    {
        return $this->checkPermission($user, 'venture.delete');
    }

    public function bulkDelete(User $user): bool
    {
        return $this->checkPermission($user, 'venture.delete');
    }
}
