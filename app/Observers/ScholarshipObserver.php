<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Scholarship;
use App\Services\Subscription\SubscriptionNotifier;

class ScholarshipObserver
{
    public function created(Scholarship $scholarship): void
    {
        $this->notifySubscribers($scholarship, null);
    }

    public function updated(Scholarship $scholarship): void
    {
        if ($scholarship->isDirty('status')) {
            $original = $scholarship->getOriginal('status');
            $this->notifySubscribers($scholarship, is_string($original) ? $original : null);
        }
    }

    private function notifySubscribers(Scholarship $scholarship, ?string $previousStatus): void
    {
        app(SubscriptionNotifier::class)->notifyScholarshipUpdate($scholarship, $previousStatus);
    }
}
