<?php

namespace App\Notifications;

use App\Models\Page;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PageStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public Page $page,
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
            'published' => __('Post ":title" has been published', ['title' => $this->page->title]),
            'approved' => __('Post ":title" has been approved', ['title' => $this->page->title]),
            'archived' => __('Post ":title" has been archived', ['title' => $this->page->title]),
            'draft', 'private' => __('Post ":title" has been unpublished', ['title' => $this->page->title]),
            default => $this->oldStatus === $this->newStatus 
                ? __('Post ":title" has been created', ['title' => $this->page->title])
                : __('Post ":title" has been edited', ['title' => $this->page->title])
        };
        
        return [
            'message' => $message,
            'post_id' => $this->page->id,
            'post_type' => $this->page->post_type,
            'news_id' => $this->page->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'author_id' => $this->page->author_id
        ];
    }
}
