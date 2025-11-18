<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Event;
use App\Services\Subscription\SubscriptionNotifier;

class EventObserver
{
    public function created(Event $event): void
    {
        if ($this->isPublished($event->status)) {
            $this->notifySubscribers($event);
        }
    }

    public function updated(Event $event): void
    {
        $originalStatus = $event->getOriginal('status');

        if ($event->isDirty('status') && $this->isPublished($event->status) && ! $this->isPublished($originalStatus)) {
            $this->notifySubscribers($event);
        }
    }

    private function notifySubscribers(Event $event): void
    {
        app(SubscriptionNotifier::class)->notifyEventPublished($event);
    }

    private function isPublished(mixed $status): bool
    {
        return is_string($status) && strcasecmp($status, Event::STATUS_PUBLISHED) === 0;
    }
}
