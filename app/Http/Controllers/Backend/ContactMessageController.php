<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', ContactMessage::class);

        $stats = [
            'total' => ContactMessage::query()->count(),
            'new' => ContactMessage::query()->new()->count(),
            'read' => ContactMessage::query()->read()->count(),
            'replied' => ContactMessage::query()->replied()->count(),
        ];

        $breadcrumbs = [
            ['title' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['title' => __('Contact Messages')],
        ];

        return view('backend.pages.contact-messages.index', compact('stats', 'breadcrumbs'));
    }

    public function show(ContactMessage $contactMessage): View
    {
        $this->authorize('view', $contactMessage);

        $contactMessage->markAsRead();

        $breadcrumbs = [
            ['title' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['title' => __('Contact Messages'), 'url' => route('admin.contact-messages.index')],
            ['title' => $contactMessage->subject],
        ];

        return view('backend.pages.contact-messages.show', compact('contactMessage', 'breadcrumbs'));
    }

    public function toggleReplied(ContactMessage $contactMessage): RedirectResponse
    {
        $this->authorize('update', $contactMessage);

        if ($contactMessage->status === 'replied') {
            $contactMessage->forceFill([
                'status' => 'read',
                'replied_at' => null,
            ])->save();

            return redirect()->back()->with('toast', [
                'variant' => 'success',
                'title' => __('Message updated'),
                'message' => __('The message has been marked as not replied.'),
            ]);
        }

        $contactMessage->markAsReplied();

        return redirect()->back()->with('toast', [
            'variant' => 'success',
            'title' => __('Message updated'),
            'message' => __('The message has been marked as replied.'),
        ]);
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $this->authorize('delete', $contactMessage);

        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')->with('toast', [
            'variant' => 'success',
            'title' => __('Message deleted'),
            'message' => __('The contact message has been deleted.'),
        ]);
    }
}
