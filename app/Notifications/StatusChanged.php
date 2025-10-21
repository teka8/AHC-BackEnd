<?php

namespace App\Notifications;

use App\Models\News;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class StatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected Post $news;
    protected string $message;
    protected string $post_type;

    public function __construct(Post $news, string $message)
    {
        $this->news = $news;
        $this->message = $message;
        $this->post_type = $news->post_type;
    }

    public function via($notifiable)
    {
        return ['database']; // saves to notifications table
    }

    public function toArray($notifiable)
    {
        return [
            'news_id' => $this->news->id,
            'post_type' => $this->post_type ?? 'news',
            'message' => $this->message,
        ];
    }
}