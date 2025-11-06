<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Scholarship;
use App\Models\User;

class ScholarshipPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'scholarship.view');
    }

    public function view(User $user, Scholarship $scholarship): bool
    {
        return $this->checkPermission($user, 'scholarship.view');
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'scholarship.create');
    }

    public function update(User $user, Scholarship $scholarship): bool
    {
        return $this->checkPermission($user, 'scholarship.update');
    }

    public function delete(User $user, Scholarship $scholarship): bool
    {
        return $this->checkPermission($user, 'scholarship.delete');
    }

    public function bulkDelete(User $user): bool
    {
        return $this->checkPermission($user, 'scholarship.delete');
    }
}
