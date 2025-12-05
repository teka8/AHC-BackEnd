<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'wants_news',
        'wants_events',
        'wants_announcements',
        'wants_scholarships',
        'wants_newsletters',
        'confirmed_at',
        'unsubscribed_at',
        'last_notified_at',
        'meta',
        'unsubscribe_token',
    ];

    protected $casts = [
        'wants_news' => 'boolean',
        'wants_events' => 'boolean',
        'wants_announcements' => 'boolean',
        'wants_scholarships' => 'boolean',
        'wants_newsletters' => 'boolean',
        'confirmed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'last_notified_at' => 'datetime',
        'meta' => 'array',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('unsubscribed_at');
    }

    public function markConfirmed(): void
    {
        if (! $this->confirmed_at) {
            $this->forceFill(['confirmed_at' => now()])->save();
        }
    }

    protected static function booted(): void
    {
        static::creating(function (EmailSubscription $subscription): void {
            if (empty($subscription->unsubscribe_token)) {
                $subscription->unsubscribe_token = $subscription->generateUnsubscribeToken();
            }
        });

        static::updating(function (EmailSubscription $subscription): void {
            if ($subscription->isDirty('email')) {
                $subscription->unsubscribe_token = $subscription->generateUnsubscribeToken();
            }
        });
    }

    public function ensureUnsubscribeToken(): string
    {
        if (empty($this->unsubscribe_token)) {
            $this->forceFill([
                'unsubscribe_token' => $this->generateUnsubscribeToken(),
            ])->saveQuietly();
        }

        return (string) $this->unsubscribe_token;
    }

    public function regenerateUnsubscribeToken(): string
    {
        $token = $this->generateUnsubscribeToken();

        $this->forceFill([
            'unsubscribe_token' => $token,
        ])->saveQuietly();

        return $token;
    }

    public function markUnsubscribed(): void
    {
        $this->forceFill([
            'unsubscribed_at' => now(),
        ])->save();
    }

    public function markSubscribed(): void
    {
        $this->forceFill([
            'unsubscribed_at' => null,
        ])->save();
    }

    protected function generateUnsubscribeToken(): string
    {
        return Str::random(64);
    }
}
