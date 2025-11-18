<?php

declare(strict_types=1);

use App\Mail\SubscriptionNotificationMail;
use App\Models\EmailSubscription;
use App\Models\Post;
use App\Services\Subscription\SubscriptionNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

it('creates a new subscription with default preferences', function () {
    $response = $this->postJson('/api/v1/subscriptions', [
        'email' => 'subscriber@gmail.com',
    ]);

    $response
        ->assertCreated()
        ->assertJsonFragment([
            'message' => 'Thank you for subscribing! You will receive updates as new content is published.',
            'email' => 'subscriber@gmail.com',
            'wants_news' => true,
        ]);

    $subscription = EmailSubscription::query()->first();

    expect($subscription)
        ->not->toBeNull()
        ->and($subscription->unsubscribe_token)->toBeString()
        ->and($subscription->wants_events)->toBeTrue()
        ->and($subscription->unsubscribed_at)->toBeNull();
});

it('re-subscribes an existing subscriber and regenerates the token', function () {
    $subscription = EmailSubscription::factory()->create([
        'email' => 'resub@gmail.com',
        'wants_news' => true,
        'unsubscribed_at' => now()->subDay(),
    ]);

    $originalToken = $subscription->unsubscribe_token;

    $response = $this->postJson('/api/v1/subscriptions', [
        'email' => 'resub@gmail.com',
        'wants_news' => true,
    ]);

    $response
        ->assertOk()
        ->assertJsonFragment([
            'email' => 'resub@gmail.com',
            'wants_news' => true,
        ]);

    $subscription->refresh();

    expect($subscription->unsubscribed_at)->toBeNull();
    expect($subscription->unsubscribe_token)->not->toBe($originalToken);
});

it('allows unsubscribing with a valid token', function () {
    $subscription = EmailSubscription::factory()->create([
        'wants_news' => true,
    ]);

    $response = $this->postJson('/api/v1/subscriptions/unsubscribe', [
        'token' => $subscription->unsubscribe_token,
    ]);

    $response
        ->assertOk()
        ->assertJsonFragment([
            'message' => 'You have been unsubscribed. We are sorry to see you go!',
        ]);

    expect($subscription->refresh()->unsubscribed_at)->not->toBeNull();
});

it('returns not found for an unknown unsubscribe token', function () {
    $response = $this->postJson('/api/v1/subscriptions/unsubscribe', [
        'token' => 'invalid-token',
    ]);

    $response
        ->assertNotFound()
        ->assertJsonFragment([
            'message' => 'This subscription could not be found. It may have already been removed.',
        ]);
});

it('queues notifications only for active subscribers with matching preferences', function () {
    Mail::fake();

    $active = EmailSubscription::factory()->create([
        'email' => 'active@example.com',
        'wants_news' => true,
        'unsubscribed_at' => null,
    ]);

    EmailSubscription::factory()->create([
        'email' => 'inactive@example.com',
        'wants_news' => true,
        'unsubscribed_at' => now(),
    ]);

    EmailSubscription::factory()->create([
        'email' => 'not-interested@example.com',
        'wants_news' => false,
    ]);

    $post = Post::factory()->createQuietly([
        'status' => 'published',
        'post_type' => 'news',
        'title' => 'Test News Story',
        'published_at' => now(),
    ]);

    app(SubscriptionNotifier::class)->notifyPostPublished($post);

    Mail::assertQueued(SubscriptionNotificationMail::class, function (SubscriptionNotificationMail $mail) use ($active) {
        $unsubscribeUrl = (function () {
            return $this->unsubscribeUrl ?? null;
        })->call($mail);

        expect($mail->hasTo($active->email))->toBeTrue();
        expect($unsubscribeUrl ?? '')->toContain($active->fresh()->unsubscribe_token);

        return true;
    });

    Mail::assertQueuedCount(1);
});
