<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\ActionType;
use App\Models\Page;
use App\Models\User;
use App\Notifications\PageStatusChanged;
use App\Concerns\HasActionLogTrait;
use Illuminate\Support\Facades\Notification;

class PageObserver
{
    use HasActionLogTrait;

    public function created(Page $page): void
    {
        $this->storeActionLog(ActionType::CREATED, ['page' => $page]);
        $this->sendNotification($page, 'page.notify_created');
    }

    public function updated(Page $page): void
    {
        $this->storeActionLog(ActionType::UPDATED, ['page' => $page]);
        
        if ($page->isDirty('status')) {
            $permission = match($page->status) {
                'published' => 'page.notify_published',
                'approved' => 'page.notify_approved',
                'archived' => 'post.notify_archived',
                'private' => 'post.notify_unpublished',
                'draft' => 'post.notify_unpublished',
                default => 'post.notify_edited'
            };
            
            $this->sendNotification($page, $permission, $page->getOriginal('status'));
        } else {
            $this->sendNotification($page, 'post.notify_edited');
        }
    }

    public function deleted(Page $page): void
    {
        $this->storeActionLog(ActionType::DELETED, ['page' => $page]);
    }
    
    private function sendNotification(Page $page, string $permission, ?string $oldStatus = null): void
    {
        $users = User::whereHas('roles', function($query) use ($permission) {
            $query->whereHas('permissions', function($q) use ($permission) {
                $q->where('name', $permission);
            });
        })->orWhereHas('permissions', function($query) use ($permission) {
            $query->where('name', $permission);
        })->get();
        
        if ($users->isNotEmpty()) {
            Notification::send($users, new PageStatusChanged(
                $page,
                $oldStatus ?? $page->status,
                $page->status
            ));
        }
    }
}
