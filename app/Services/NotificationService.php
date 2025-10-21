<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Post;

class NotificationService
{
    public function createNewsNotification(Post $news): void
    {
        // Get all users (simplified for testing)
        $users = User::take(10)->get(); // Limit to first 10 users for testing
        
        $authorName = $news->user ? $news->user->full_name : 'Unknown';
        
        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'news_created',
                'title' => 'New News Article Created',
                'message' => "New news article '{$news->title}' has been created by {$authorName}",
                'data' => [
                    'news_id' => $news->id,
                    'news_title' => $news->title,
                    'author' => $authorName
                ]
            ]);
        }
    }

    public function createStatusChangeNotification(Post $news, string $oldStatus, string $newStatus): void
    {
        $users = collect();
        
        // Get admins
        $admins = User::role('admin')->get();
        if ($admins->isEmpty()) {
            $admins = User::permission('admin')->get();
        }
        if ($admins->isEmpty()) {
            $admins = User::all();
        }
        
        // Notify admins and the author
        $users = $users->merge($admins);
        if ($news->user) {
            $users = $users->merge([$news->user]);
        }
        $users = $users->unique('id');

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'news_status_changed',
                'title' => 'News Article Status Changed',
                'message' => "News article '{$news->title}' status changed from {$oldStatus} to {$newStatus}",
                'data' => [
                    'news_id' => $news->id,
                    'news_title' => $news->title,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ]
            ]);
        }
    }

    public function getUnreadNotifications(User $user)
    {
        return $user->notifications()->whereNull('read_at')->latest()->take(10)->get();
    }

    public function getUnreadCount(User $user): int
    {
        return $user->notifications()->whereNull('read_at')->count();
    }
}