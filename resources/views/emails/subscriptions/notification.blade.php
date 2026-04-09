<x-mail::message>
@if(! empty($previewText))
<span style="display: none !important; visibility: hidden; opacity: 0; height: 0; width: 0;">
    {{ $previewText }}
</span>
@endif

# {{ $headline }}

<p style="font-size: 16px; line-height: 1.5em; color: #374151;">{{ $intro }}</p>

<x-mail::button :url="$actionUrl" color="primary">
{{ $actionText }}
</x-mail::button>

@if(! empty($meta))
<x-mail::panel>
@foreach($meta as $label => $value)
**{{ $label }}:** {{ $value }}

@endforeach
</x-mail::panel>
@endif

@if(! empty($unsubscribeUrl))
@slot('subcopy')
{{ __('If you no longer wish to receive these updates, you can [:unsubscribe here](:url).', ['url' => $unsubscribeUrl]) }}
@endslot
@endif
</x-mail::message>
