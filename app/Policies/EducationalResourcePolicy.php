<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\EducationalResource;
use App\Models\User;

class EducationalResourcePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'educational_resource.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EducationalResource $document): bool
    {
        // Users can view if they have general view permission OR own educational hub document view permission
        $canView = $this->checkPermission($user, 'educational_resource.view') ||
            ($this->checkPermission($user, 'educational_resource.view.own') && $document->created_by === $user->id);

        // Additionally check access level restrictions
        if ($canView) {
            return $document->isAccessibleBy($user);
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'educational_resource.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EducationalResource $document): bool
    {
        // Users can update if they have general edit permission OR own document edit permission
        $canUpdate = $this->checkPermission($user, 'educational_resource.edit') ||
            ($this->checkPermission($user, 'educational_resource.edit.own') && $document->created_by === $user->id);

        // Additionally check if document is in a state that can be edited
        if ($canUpdate) {
            return in_array($document->status, [
                EducationalResource::STATUS_DRAFT,
                EducationalResource::STATUS_UNDER_REVIEW
            ]) || $this->checkPermission($user, 'educational_resource.edit'); // Admins can edit any status
        }

        return false;
    }

    public function delete(User $user, EducationalResource $document)
    {
        return $user->hasPermission('educational_resource.delete') ||
            ($user->hasPermission('educational_resource.delete.own') && $document->created_by === $user->id);
    }

    public function forceDelete(User $user, EducationalResource $document)
    {
        // Only super admins can force delete
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, EducationalResource $document)
    {
        return $user->hasPermission('educational_resource.delete') || $user->hasRole('super_admin');
    }



    /**
     * Determine whether the user can bulk delete models.
     */
    public function bulkDelete(User $user): bool
    {
        return $this->checkPermission($user, 'educational_resource.delete');
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, EducationalResource $document): bool
    {
        return $this->checkPermission($user, 'educational_resource.publish') &&
            $document->status === EducationalResource::STATUS_APPROVED;
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, EducationalResource $document): bool
    {
        return $this->checkPermission($user, 'educational_resource.approve') &&
            $document->status === EducationalResource::STATUS_UNDER_REVIEW;
    }

    /**
     * Determine whether the user can review the model.
     */
    public function review(User $user, EducationalResource $document): bool
    {
        return $this->checkPermission($user, 'educational_resource.review') &&
            $document->status === EducationalResource::STATUS_DRAFT;
    }

    /**
     * Determine whether the user can feature the model.
     */
    public function feature(User $user, EducationalResource $document): bool
    {
        return $this->checkPermission($user, 'educational_resource.feature') &&
            $document->status === EducationalResource::STATUS_PUBLISHED;
    }

    /**
     * Determine whether the user can archive the model.
     */
    public function archive(User $user, EducationalResource $document): bool
    {
        return $this->checkPermission($user, 'educational_resource.archive') &&
            in_array($document->status, [
                EducationalResource::STATUS_PUBLISHED,
                EducationalResource::STATUS_APPROVED
            ]);
    }


    /**
     * Determine whether the user can download the model.
     */
    public function download(User $user, EducationalResource $document): bool
    {
        // Users can download if they can view the document
        return $this->view($user, $document);
    }

    /**
     * Determine whether the user can manage document categories.
     */
    public function manageCategories(User $user): bool
    {
        return $this->checkPermission($user, 'educational_category.create') ||
            $this->checkPermission($user, 'educational_category.edit') ||
            $this->checkPermission($user, 'educational_category.delete');
    }

    /**
     * Determine whether the user can view document analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $this->checkPermission($user, 'educational_resource.view.analytics');
    }

    /**
     * Determine whether the user can perform bulk operations.
     */
    public function bulkOperations(User $user): bool
    {
        return $this->checkPermission($user, 'educational_resource.bulk.operations');
    }

    /**
     * Determine whether the user can manage document access.
     */
    public function manageAccess(User $user, EducationalResource $document): bool
    {
        return $this->checkPermission($user, 'educational_resource.manage.access');
    }

    /**
     * Determine whether the user can update document version.
     */
    public function updateVersion(User $user, EducationalResource $document): bool
    {
        return $this->checkPermission($user, 'educational_resource.version') &&
            $this->update($user, $document);
    }
}