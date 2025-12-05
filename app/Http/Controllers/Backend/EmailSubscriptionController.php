<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\EmailSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmailSubscriptionController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', EmailSubscription::class);

        $activeQuery = EmailSubscription::query()->active();

        $stats = [
            'total' => EmailSubscription::query()->count(),
            'active' => (clone $activeQuery)->count(),
            'unsubscribed' => EmailSubscription::query()->whereNotNull('unsubscribed_at')->count(),
            'wants_news' => (clone $activeQuery)->where('wants_news', true)->count(),
            'wants_events' => (clone $activeQuery)->where('wants_events', true)->count(),
            'wants_announcements' => (clone $activeQuery)->where('wants_announcements', true)->count(),
            'wants_scholarships' => (clone $activeQuery)->where('wants_scholarships', true)->count(),
        ];

        $breadcrumbs = [
            ['title' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['title' => __('Subscribers')],
        ];

        return view('backend.pages.subscriptions.index', compact('stats', 'breadcrumbs'));
    }

    public function unsubscribe(EmailSubscription $subscription): RedirectResponse
    {
        $this->authorize('update', $subscription);

        if (! $subscription->unsubscribed_at) {
            $subscription->markUnsubscribed();
        }

        return redirect()->back()->with('toast', [
            'variant' => 'success',
            'title' => __('Subscriber updated'),
            'message' => __('The subscriber has been marked as unsubscribed.'),
        ]);
    }

    public function resubscribe(EmailSubscription $subscription): RedirectResponse
    {
        $this->authorize('update', $subscription);

        if ($subscription->unsubscribed_at) {
            $subscription->markSubscribed();
            $subscription->regenerateUnsubscribeToken();
        }

        return redirect()->back()->with('toast', [
            'variant' => 'success',
            'title' => __('Subscriber updated'),
            'message' => __('The subscriber has been marked as active.'),
        ]);
    }


    public function export(\Illuminate\Http\Request $request)
    {
        $this->authorize('viewAny', EmailSubscription::class);

        $selectedIds = $request->input('selected', []);
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SubscribersExport($selectedIds),
            'subscribers-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function destroy(EmailSubscription $subscription): RedirectResponse
    {
        $this->authorize('delete', $subscription);

        $subscription->delete();

        return redirect()->back()->with('toast', [
            'variant' => 'success',
            'title' => __('Subscriber deleted'),
            'message' => __('The subscriber has been removed.'),
        ]);
    }
}
