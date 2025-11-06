<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ScholarshipApplication;
use App\Models\User;

class ScholarshipApplicationPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'scholarship_application.view');
    }

    public function view(User $user, ScholarshipApplication $scholarshipApplication): bool
    {
        return $this->checkPermission($user, 'scholarship_application.view');
    }

    public function update(User $user, ScholarshipApplication $scholarshipApplication): bool
    {
        return $this->checkPermission($user, 'scholarship_application.update');
    }

    public function delete(User $user, ScholarshipApplication $scholarshipApplication): bool
    {
        return $this->checkPermission($user, 'scholarship_application.delete');
    }

    public function bulkDelete(User $user): bool
    {
        return $this->checkPermission($user, 'scholarship_application.delete');
    }
}
