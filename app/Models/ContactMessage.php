<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'subject',
        'message',
        'status',
        'ip_address',
        'user_agent',
        'read_at',
        'replied_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'replied_at' => 'datetime',
    ];

    public function scopeNew(Builder $query): Builder
    {
        return $query->where('status', 'new');
    }

    public function scopeRead(Builder $query): Builder
    {
        return $query->where('status', 'read');
    }

    public function scopeReplied(Builder $query): Builder
    {
        return $query->where('status', 'replied');
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('status', 'new');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function markAsRead(): void
    {
        if ($this->status === 'new') {
            $this->forceFill([
                'status' => 'read',
                'read_at' => now(),
            ])->save();
        }
    }

    public function markAsReplied(): void
    {
        $this->forceFill([
            'status' => 'replied',
            'replied_at' => now(),
        ])->save();
    }

    public function getParsedUserAgentAttribute(): array
    {
        $userAgent = $this->user_agent ?? '';
        
        if (empty($userAgent)) {
            return [
                'browser' => 'Unknown',
                'os' => 'Unknown',
                'device' => 'Unknown',
            ];
        }

        // Detect Browser
        $browser = 'Unknown';
        if (preg_match('/Edg\/([0-9.]+)/', $userAgent, $matches)) {
            $browser = 'Microsoft Edge ' . explode('.', $matches[1])[0];
        } elseif (preg_match('/OPR\/([0-9.]+)/', $userAgent, $matches)) {
            $browser = 'Opera ' . explode('.', $matches[1])[0];
        } elseif (preg_match('/Chrome\/([0-9.]+)/', $userAgent, $matches)) {
            $browser = 'Chrome ' . explode('.', $matches[1])[0];
        } elseif (preg_match('/Firefox\/([0-9.]+)/', $userAgent, $matches)) {
            $browser = 'Firefox ' . explode('.', $matches[1])[0];
        } elseif (preg_match('/Safari\/([0-9.]+)/', $userAgent, $matches) && !str_contains($userAgent, 'Chrome')) {
            if (preg_match('/Version\/([0-9.]+)/', $userAgent, $versionMatches)) {
                $browser = 'Safari ' . explode('.', $versionMatches[1])[0];
            } else {
                $browser = 'Safari';
            }
        } elseif (preg_match('/MSIE ([0-9.]+)/', $userAgent, $matches)) {
            $browser = 'Internet Explorer ' . explode('.', $matches[1])[0];
        } elseif (str_contains($userAgent, 'Trident/')) {
            $browser = 'Internet Explorer 11';
        }

        // Detect OS
        $os = 'Unknown';
        if (str_contains($userAgent, 'Windows NT 10.0')) {
            $os = 'Windows 10/11';
        } elseif (str_contains($userAgent, 'Windows NT 6.3')) {
            $os = 'Windows 8.1';
        } elseif (str_contains($userAgent, 'Windows NT 6.2')) {
            $os = 'Windows 8';
        } elseif (str_contains($userAgent, 'Windows NT 6.1')) {
            $os = 'Windows 7';
        } elseif (str_contains($userAgent, 'Mac OS X')) {
            if (preg_match('/Mac OS X ([0-9_]+)/', $userAgent, $matches)) {
                $version = str_replace('_', '.', $matches[1]);
                $os = 'macOS ' . $version;
            } else {
                $os = 'macOS';
            }
        } elseif (str_contains($userAgent, 'Android')) {
            if (preg_match('/Android ([0-9.]+)/', $userAgent, $matches)) {
                $os = 'Android ' . explode('.', $matches[1])[0];
            } else {
                $os = 'Android';
            }
        } elseif (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) {
            if (preg_match('/OS ([0-9_]+)/', $userAgent, $matches)) {
                $version = str_replace('_', '.', $matches[1]);
                $os = 'iOS ' . explode('.', $version)[0];
            } else {
                $os = 'iOS';
            }
        } elseif (str_contains($userAgent, 'Linux')) {
            $os = 'Linux';
        }

        // Add architecture if available
        if (str_contains($userAgent, 'Win64') || str_contains($userAgent, 'x64') || str_contains($userAgent, 'x86_64')) {
            $os .= ' (64-bit)';
        } elseif (str_contains($userAgent, 'WOW64') || str_contains($userAgent, 'i686') || str_contains($userAgent, 'i386')) {
            $os .= ' (32-bit)';
        }

        // Detect Device Type
        $device = 'Desktop';
        if (str_contains($userAgent, 'Mobile') || str_contains($userAgent, 'Android') && !str_contains($userAgent, 'Tablet')) {
            $device = 'Mobile';
        } elseif (str_contains($userAgent, 'iPad') || str_contains($userAgent, 'Tablet')) {
            $device = 'Tablet';
        } elseif (str_contains($userAgent, 'iPhone')) {
            $device = 'iPhone';
        }

        return [
            'browser' => $browser,
            'os' => $os,
            'device' => $device,
        ];
    }
}
