<?php

declare(strict_types=1);

namespace App\Services\Subscription;

use App\Mail\SubscriptionNotificationMail;
use App\Models\EmailSubscription;
use App\Models\Event;
use App\Models\Post;
use App\Models\Scholarship;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SubscriptionNotifier
{
    /**
     * Notify subscribers when a post (news or announcement) is published.
     */
    public function notifyPostPublished(Post $post): void
    {
        $type = strtolower((string) ($post->post_type ?? ''));

        if (! in_array($type, ['news', 'announcement'], true)) {
            return;
        }

        $subject = $type === 'announcement'
            ? __('New announcement: :title', ['title' => $post->title])
            : __('New story published: :title', ['title' => $post->title]);

        $headline = $post->title ?? ($type === 'announcement' ? __('Latest announcement from AHC - AAU') : __('Latest news from AHC - AAU'));
        $intro = $this->summarize($post->excerpt, $post->content);
        $path = $type === 'announcement' ? "announcement/{$post->getKey()}" : "news/{$post->getKey()}";
        $meta = [];

        if ($post->published_at instanceof Carbon) {
            $meta[__('Published on')] = $this->formatDate($post->published_at);
        }

        $meta[__('Category')] = ucfirst($type);

        $this->notifySubscribers(
            $type === 'announcement' ? 'wants_announcements' : 'wants_news',
            $subject,
            $headline,
            $intro,
            $path,
            $type === 'announcement' ? __('View announcement') : __('Read full story'),
            $meta,
        );
    }

    /**
     * Notify subscribers when an event is published.
     */
    public function notifyEventPublished(Event $event): void
    {
        $subject = __('Upcoming event: :title', ['title' => $event->title]);
        $headline = $event->title ?? __('New event from AHC - AAU');
        $intro = $this->summarize($event->description, null);
        $path = "events/{$event->getKey()}";

        $meta = [];
        if ($event->event_date) {
            $meta[__('Event date')] = $this->formatDate(Carbon::parse($event->event_date));
        }

        if (! empty($event->start_time)) {
            $meta[__('Starts at')] = Carbon::parse($event->start_time)->format('g:i A');
        }

        if (! empty($event->location)) {
            $meta[__('Location')] = $event->location;
        }

        $this->notifySubscribers(
            'wants_events',
            $subject,
            $headline,
            $intro,
            $path,
            __('View event details'),
            $meta,
        );
    }

    /**
     * Notify subscribers when a scholarship is created or its status changes.
     */
    public function notifyScholarshipUpdate(Scholarship $scholarship, ?string $previousStatus = null): void
    {
        $currentStatus = $this->formatScholarshipStatus((string) $scholarship->status);

        $subject = $previousStatus === null
            ? __('New scholarship opportunity: :title', ['title' => $scholarship->title])
            : __('Scholarship update: :title is now :status', [
                'title' => $scholarship->title,
                'status' => $currentStatus,
            ]);

        $headline = $scholarship->title ?? __('Scholarship opportunity at AHC - AAU');
        $intro = $this->summarize($scholarship->description, null);
        $path = "scholarship/{$scholarship->getKey()}";

        $meta = [
            __('Status') => $currentStatus,
        ];

        if ($scholarship->deadline) {
            $meta[__('Deadline')] = $this->formatDate(Carbon::parse($scholarship->deadline));
        }

        if ($scholarship->application_start_date) {
            $meta[__('Applications open')] = $this->formatDate(Carbon::parse($scholarship->application_start_date));
        }

        $this->notifySubscribers(
            'wants_scholarships',
            $subject,
            $headline,
            $intro,
            $path,
            __('View scholarship'),
            $meta,
        );
    }

    private function notifySubscribers(
        string $preferenceColumn,
        string $subject,
        string $headline,
        string $intro,
        string $path,
        string $actionText,
        array $meta = []
    ): void {
        $actionUrl = $this->buildFrontendUrl($path);
        $previewText = Str::limit(strip_tags($intro), 150);
        $now = now();

        EmailSubscription::query()
            ->active()
            ->whereNotNull('confirmed_at')
            ->where($preferenceColumn, true)
            ->chunkById(100, function (Collection $subscriptions) use ($subject, $headline, $intro, $actionUrl, $actionText, $meta, $previewText, $now) {
                $ids = [];

                foreach ($subscriptions as $subscription) {
                    $token = $subscription->ensureUnsubscribeToken();
                    $unsubscribeUrl = $this->buildFrontendUrl('unsubscribe?token=' . urlencode($token));

                    Mail::to($subscription->email)->queue(
                        new SubscriptionNotificationMail(
                            $subject,
                            $headline,
                            $intro,
                            $actionUrl,
                            $actionText,
                            $meta,
                            $previewText,
                            $unsubscribeUrl,
                        )
                    );

                    $ids[] = $subscription->id;
                }

                if (! empty($ids)) {
                    EmailSubscription::query()->whereIn('id', $ids)->update([
                        'last_notified_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            });
    }

    private function buildFrontendUrl(string $path): string
    {
        $base = config('app.frontend_url')
            ?? config('services.frontend.url')
            ?? env('FRONTEND_URL', config('app.url'));

        return rtrim($base ?? '', '/') . '/' . ltrim($path, '/');
    }

    private function formatDate(?Carbon $date): ?string
    {
        if (! $date) {
            return null;
        }

        return $date->copy()->timezone(config('app.timezone', 'UTC'))->format('F j, Y');
    }

    private function summarize(?string $excerpt, ?string $content): string
    {
        $text = $excerpt
            ?: ($content ? Str::of(strip_tags($content))->squish()->toString() : '');

        if ($text === '') {
            $text = __('Stay tuned for more details from the Africa Health Collaborative - AAU.');
        }

        return $text;
    }

    private function formatScholarshipStatus(string $status): string
    {
        return match (strtolower($status)) {
            'open' => __('Open'),
            'closed' => __('Closed'),
            'upcoming' => __('Upcoming'),
            default => Str::headline($status),
        };
    }
}
