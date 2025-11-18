<x-mail::message>
# A New {{ Str::studly(class_basename($content)) }} has been published!

Hello {{ $subscriber->name ?? 'Subscriber' }},

We are excited to share some new content with you.

## {{ $content->title }}

@if($content->excerpt)
<p>{{ $content->excerpt }}</p>
@elseif($content->description)
<p>{{ Str::limit($content->description, 200) }}</p>
@endif

<x-mail::button :url="url('/' . Str::kebab(class_basename($content)) . '/' . $content->id)">
View {{ Str::studly(class_basename($content)) }}
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}

<x-slot:footer>
<x-mail::footer>
If you no longer wish to receive these emails, you can <a href="{{ route('api.subscriptions.unsubscribe', ['token' => $subscriber->unsubscribe_token]) }}">unsubscribe here</a>.
</x-mail::footer>
</x-slot:footer>
</x-mail::message>
