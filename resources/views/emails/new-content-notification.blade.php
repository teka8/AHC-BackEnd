<x-mail::message>
@php
    $isNewsletter = $content instanceof \App\Models\Others && $content->resource_type === \App\Models\Others::TYPE_NEWSLETTER;
    $contentType = $isNewsletter ? 'Newsletter' : Str::studly(class_basename($content));
@endphp
# A New {{ $contentType }} has been published!

Hello {{ $subscriber->name ?? 'Subscriber' }},

We are excited to share some new content with you.

## {{ $content->title }}

@if($content->excerpt)
<p>{{ $content->excerpt }}</p>
@elseif($content->description)
<p>{{ Str::limit($content->description, 200) }}</p>
@endif

@if($isNewsletter)
<x-mail::button :url="config('app.frontend_url') . '/resources#others'">
View Newsletter
</x-mail::button>

<x-mail::button :url="url('/api/v1/public/resources/others/' . $content->id . '/file')" color="success">
Download Newsletter
</x-mail::button>
@else
<x-mail::button :url="url('/' . Str::kebab(class_basename($content)) . '/' . $content->id)">
View {{ $contentType }}
</x-mail::button>
@endif

Thanks,<br>
{{ config('app.name') }}

<x-slot:footer>
<x-mail::footer>
If you no longer wish to receive these emails, you can <a href="{{ route('api.subscriptions.unsubscribe', ['token' => $subscriber->unsubscribe_token]) }}">unsubscribe here</a>.
</x-mail::footer>
</x-slot:footer>
</x-mail::message>
