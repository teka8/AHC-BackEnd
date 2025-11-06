<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\VentureApplication;
use App\Models\User;

class VentureApplicationPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'venture_application.view');
    }

    public function view(User $user, VentureApplication $ventureApplication): bool
    {
        return $this->checkPermission($user, 'venture_application.view');
    }

    public function update(User $user, VentureApplication $ventureApplication): bool
    {
        return $this->checkPermission($user, 'venture_application.update');
    }

    public function delete(User $user, VentureApplication $ventureApplication): bool
    {
        return $this->checkPermission($user, 'venture_application.delete');
    }

    public function bulkDelete(User $user): bool
    {
        return $this->checkPermission($user, 'venture_application.delete');
    }
}
