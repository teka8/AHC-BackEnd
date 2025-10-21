<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {
    }

    public function index(Request $request)
    {
        $notifications = auth()->user()->notifications()
            ->latest()
            ->paginate(20);

        $this->setBreadcrumbTitle(__('Notifications'));

        return $this->renderViewWithBreadcrumbs('backend.pages.notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification): RedirectResponse
    {
        $this->authorize('update', $notification);
        
        $notification->markAsRead();

        return back()->with('success', __('Notification marked as read'));
    }

    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', __('All notifications marked as read'));
    }
}