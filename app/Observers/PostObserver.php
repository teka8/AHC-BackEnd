<?php

declare(strict_types=1);

namespace App\Observers;

use App\Concerns\HasActionLogTrait;
use App\Enums\ActionType;
use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use App\Notifications\PostStatusChanged;
use App\Services\Subscription\SubscriptionNotifier;
use Illuminate\Support\Facades\Notification;

class PostObserver
{
    use HasActionLogTrait;

    public function created(Post $post): void
    {
        $this->storeActionLog(ActionType::CREATED, ['post' => $post]);
        $this->sendNotification($post, 'post.notify_created');
        $this->maybeNotifySubscribers($post, null);
    }

    public function updated(Post $post): void
    {
        $this->storeActionLog(ActionType::UPDATED, ['post' => $post]);
        $originalStatus = $post->getOriginal('status');

        if ($post->isDirty('status')) {
            $permission = match($post->status) {
                'published' => 'post.notify_published',
                'approved' => 'post.notify_approved',
                'archived' => 'post.notify_archived',
                'private' => 'post.notify_unpublished',
                'draft' => 'post.notify_unpublished',
                default => 'post.notify_edited'
            };

            $this->sendNotification($post, $permission, $originalStatus);
        } else {
            $this->sendNotification($post, 'post.notify_edited');
        }

        $this->maybeNotifySubscribers($post, $originalStatus);
    }

    public function deleted(Post $post): void
    {
        $this->storeActionLog(ActionType::DELETED, ['post' => $post]);
    }

    private function sendNotification(Post $post, string $permission, ?string $oldStatus = null): void
    {
        $users = User::whereHas('roles', function ($query) use ($permission) {
            $query->whereHas('permissions', function ($q) use ($permission) {
                $q->where('name', $permission);
            });
        })->orWhereHas('permissions', function ($query) use ($permission) {
            $query->where('name', $permission);
        })->get();

        if ($users->isNotEmpty()) {
            Notification::send($users, new PostStatusChanged(
                $post,
                $oldStatus ?? $post->status,
                $post->status
            ));
        }
    }

    private function maybeNotifySubscribers(Post $post, ?string $oldStatus): void
    {
        if (strcasecmp((string) $post->status, PostStatus::PUBLISHED->value) !== 0) {
            return;
        }

        if ($oldStatus !== null && strcasecmp($oldStatus, PostStatus::PUBLISHED->value) === 0) {
            return;
        }

        app(SubscriptionNotifier::class)->notifyPostPublished($post);
    }
}
