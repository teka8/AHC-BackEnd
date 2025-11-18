<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreEmailSubscriptionRequest;
use App\Http\Requests\Api\UnsubscribeEmailSubscriptionRequest;
use App\Models\EmailSubscription;
use Illuminate\Http\JsonResponse;

class EmailSubscriptionController extends Controller
{
    public function store(StoreEmailSubscriptionRequest $request): JsonResponse
    {
        $data = $request->safe()->only([
            'email',
            'name',
            'wants_news',
            'wants_events',
            'wants_announcements',
            'wants_scholarships',
        ]);

        $preferences = collect($data)
            ->only(['wants_news', 'wants_events', 'wants_announcements', 'wants_scholarships'])
            ->map(fn ($value) => (bool) $value)
            ->all();

        $attributes = array_merge(
            $preferences,
            ['name' => $data['name'] ?? null]
        );

        $subscription = EmailSubscription::query()->where('email', $data['email'])->first();
        $status = 200;

        if ($subscription) {
            $subscription->fill($attributes);
            $subscription->forceFill([
                'unsubscribed_at' => null,
                'confirmed_at' => $subscription->confirmed_at ?? now(),
                'meta' => array_merge($subscription->meta ?? [], [
                    'last_ip' => $request->ip(),
                    'last_user_agent' => $request->userAgent(),
                ]),
            ]);
            $subscription->save();
            $subscription->regenerateUnsubscribeToken();
        } else {
            $subscription = EmailSubscription::query()->create(array_merge([
                'email' => $data['email'],
                'confirmed_at' => now(),
                'meta' => [
                    'created_ip' => $request->ip(),
                    'created_user_agent' => $request->userAgent(),
                ],
            ], $attributes));

            $status = 201;
        }

        return response()->json([
            'message' => __('Thank you for subscribing! You will receive updates as new content is published.'),
            'subscription' => [
                'email' => $subscription->email,
                'wants_news' => $subscription->wants_news,
                'wants_events' => $subscription->wants_events,
                'wants_announcements' => $subscription->wants_announcements,
                'wants_scholarships' => $subscription->wants_scholarships,
            ],
        ], $status);
    }

    public function unsubscribe(UnsubscribeEmailSubscriptionRequest $request): JsonResponse
    {
        $subscription = EmailSubscription::query()
            ->where('unsubscribe_token', $request->validated('token'))
            ->first();

        if (! $subscription) {
            return response()->json([
                'message' => __('This subscription could not be found. It may have already been removed.'),
            ], 404);
        }

        if ($subscription->unsubscribed_at) {
            return response()->json([
                'message' => __('You have already unsubscribed from future updates. If this was a mistake, you can subscribe again at any time.'),
            ]);
        }

        $subscription->markUnsubscribed();

        return response()->json([
            'message' => __('You have been unsubscribed. We are sorry to see you go!'),
        ]);
    }
}
