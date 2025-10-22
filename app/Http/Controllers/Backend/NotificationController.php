<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;

class NotificationController extends Controller
{
    public function index(): Renderable
    {
        $this->setBreadcrumbTitle(__('Notifications'));
        
        $notifications = auth()->user()->notifications()->paginate(20);
        
        return $this->renderViewWithBreadcrumbs('backend.pages.notifications.index', [
            'notifications' => $notifications
        ]);
    }
    
    public function markAsRead($id)
    {
        $notification = auth()->user()->unreadNotifications()->findOrFail($id);
        $notification->markAsRead();
        
        return response()->noContent();
    }
    
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        
        return redirect()->back()->with('success', __('All notifications marked as read'));
    }
}
