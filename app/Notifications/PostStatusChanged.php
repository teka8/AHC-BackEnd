<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public Post $post,
        public string $oldStatus,
        public string $newStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $message = match($this->newStatus) {
            'published' => __('Post ":title" has been published', ['title' => $this->post->title]),
            'approved' => __('Post ":title" has been approved', ['title' => $this->post->title]),
            'archived' => __('Post ":title" has been archived', ['title' => $this->post->title]),
            'draft', 'private' => __('Post ":title" has been unpublished', ['title' => $this->post->title]),
            default => $this->oldStatus === $this->newStatus 
                ? __('Post ":title" has been created', ['title' => $this->post->title])
                : __('Post ":title" has been edited', ['title' => $this->post->title])
        };
        
        return [
            'message' => $message,
            'post_id' => $this->post->id,
            'post_type' => $this->post->post_type,
            'news_id' => $this->post->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'author_id' => $this->post->author_id
        ];
    }
}
