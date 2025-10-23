<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'document.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Document $document): bool
    {
        // Users can view if they have general view permission OR own document view permission
        $canView = $this->checkPermission($user, 'document.view') || 
                  ($this->checkPermission($user, 'document.view.own') && $document->created_by === $user->id);

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
        return $this->checkPermission($user, 'document.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Document $document): bool
    {
        // Users can update if they have general edit permission OR own document edit permission
        $canUpdate = $this->checkPermission($user, 'document.edit') || 
                    ($this->checkPermission($user, 'document.edit.own') && $document->created_by === $user->id);

        // Additionally check if document is in a state that can be edited
        if ($canUpdate) {
            return in_array($document->status, [
                Document::STATUS_DRAFT,
                Document::STATUS_UNDER_REVIEW
            ]) || $this->checkPermission($user, 'document.edit'); // Admins can edit any status
        }

        return false;
    }

    public function delete(User $user, Document $document)
    {
        return $user->hasPermission('document.delete') || 
            ($user->hasPermission('document.delete.own') && $document->created_by === $user->id);
    }

    public function forceDelete(User $user, Document $document)
    {
        // Only super admins can force delete
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, Document $document)
    {
        return $user->hasPermission('document.delete') || $user->hasRole('super_admin');
    }

    

    /**
     * Determine whether the user can bulk delete models.
     */
    public function bulkDelete(User $user): bool
    {
        return $this->checkPermission($user, 'document.delete');
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, Document $document): bool
    {
        return $this->checkPermission($user, 'document.publish') && 
               $document->status === Document::STATUS_APPROVED;
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, Document $document): bool
    {
        return $this->checkPermission($user, 'document.approve') && 
               $document->status === Document::STATUS_UNDER_REVIEW;
    }

    /**
     * Determine whether the user can review the model.
     */
    public function review(User $user, Document $document): bool
    {
        return $this->checkPermission($user, 'document.review') && 
               $document->status === Document::STATUS_DRAFT;
    }

    /**
     * Determine whether the user can feature the model.
     */
    public function feature(User $user, Document $document): bool
    {
        return $this->checkPermission($user, 'document.feature') && 
               $document->status === Document::STATUS_PUBLISHED;
    }

    /**
     * Determine whether the user can archive the model.
     */
    public function archive(User $user, Document $document): bool
    {
        return $this->checkPermission($user, 'document.archive') && 
               in_array($document->status, [
                   Document::STATUS_PUBLISHED,
                   Document::STATUS_APPROVED
               ]);
    }


    /**
     * Determine whether the user can download the model.
     */
    public function download(User $user, Document $document): bool
    {
        // Users can download if they can view the document
        return $this->view($user, $document);
    }

    /**
     * Determine whether the user can manage document categories.
     */
    public function manageCategories(User $user): bool
    {
        return $this->checkPermission($user, 'document_category.create') || 
               $this->checkPermission($user, 'document_category.edit') || 
               $this->checkPermission($user, 'document_category.delete');
    }

    /**
     * Determine whether the user can view document analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $this->checkPermission($user, 'document.view.analytics');
    }

    /**
     * Determine whether the user can perform bulk operations.
     */
    public function bulkOperations(User $user): bool
    {
        return $this->checkPermission($user, 'document.bulk.operations');
    }

    /**
     * Determine whether the user can manage document access.
     */
    public function manageAccess(User $user, Document $document): bool
    {
        return $this->checkPermission($user, 'document.manage.access');
    }

    /**
     * Determine whether the user can update document version.
     */
    public function updateVersion(User $user, Document $document): bool
    {
        return $this->checkPermission($user, 'document.version') && 
               $this->update($user, $document);
    }
}