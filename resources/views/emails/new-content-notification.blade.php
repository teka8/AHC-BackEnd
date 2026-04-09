<x-mail::message>
@php
    $isNewsletter = $content instanceof \App\Models\Others && $content->resource_type === \App\Models\Others::TYPE_NEWSLETTER;
    $contentType = $isNewsletter ? 'Newsletter' : Str::studly(class_basename($content));
@endphp
# A New {{ $contentType }} has been published!

Hello!

We are excited to share some new content with you.

## {{ $content->title }}

@if($isNewsletter)
<p style="font-size: 14px; color: #6b7280; font-style: italic; margin-top: -10px; margin-bottom: 20px;">
    By {{ $content->creator ?? 'AHC' }} • {{ $content->published_at ? $content->published_at->format('F j, Y') : now()->format('F j, Y') }}
</p>
@endif

@if($content->excerpt)
<p style="font-size: 16px; line-height: 1.5em; color: #374151;">{{ $content->excerpt }}</p>
@elseif($content->description)
<p style="font-size: 16px; line-height: 1.5em; color: #374151;">{{ $content->description }}</p>
@endif

@if($isNewsletter)
<x-mail::button :url="config('app.frontend_url') . '/resources#others'" color="primary">
View Newsletter
</x-mail::button>

<x-mail::button :url="url('/api/v1/public/resources/others/' . $content->id . '/file')" color="success">
Download Newsletter
</x-mail::button>
@else
@php
    $path = Str::kebab(class_basename($content));
    if ($content instanceof \App\Models\Post) {
        $path = $content->post_type === 'announcement' ? 'announcements' : 'news';
    } elseif ($content instanceof \App\Models\Event) {
        $path = 'events';
    } elseif ($content instanceof \App\Models\Scholarship) {
        $path = 'scholarships';
    }
@endphp
<x-mail::button :url="config('app.frontend_url') . '/' . $path . '/' . $content->id" color="primary">
View {{ $contentType }}
</x-mail::button>
@endif

</x-mail::message>
