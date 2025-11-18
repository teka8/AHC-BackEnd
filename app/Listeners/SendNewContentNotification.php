<?php

namespace App\Listeners;

use App\Events\NewContentPublished;
use App\Mail\NewContentNotification;
use App\Models\EmailSubscription;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class SendNewContentNotification implements ShouldQueue
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public ?string $queue = 'emails';

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewContentPublished $event): void
    {
        $subscribers = EmailSubscription::query()
            ->active()
            ->where(function (Builder $query) use ($event) {
                $query->where("wants_{$event->type}", true);
            })
            ->get();

        foreach ($subscribers as $subscriber) {
            try {
                Mail::to($subscriber->email)->queue(new NewContentNotification($event->content, $subscriber));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send new content notification to ' . $subscriber->email . ': ' . $e->getMessage());
            }
        }
    }
}
