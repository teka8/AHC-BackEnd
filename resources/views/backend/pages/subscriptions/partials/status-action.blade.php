@php
    $isUnsubscribed = ! is_null($subscription->unsubscribed_at);
    $action = $isUnsubscribed ? 'resubscribe' : 'unsubscribe';
    $label = $isUnsubscribed ? __('Resubscribe') : __('Unsubscribe');
    $icon = $isUnsubscribed ? 'lucide:refresh-ccw' : 'lucide:mail-x';
@endphp

<x-buttons.action-item
    type="button"
    :icon="$icon"
    :label="$label"
    wire:click="{{ $action }}({{ $subscription->id }})"
/>
