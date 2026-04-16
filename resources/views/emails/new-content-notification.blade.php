<x-mail::message>
@php
$isNewsletter = $content instanceof \App\Models\Others && $content->resource_type === \App\Models\Others::TYPE_NEWSLETTER;
$contentType = $isNewsletter ? 'Newsletter' : Str::studly(class_basename($content));
@endphp

@if($isNewsletter)
@php
$volume = $content->newsletter_volume;
$issue = $content->newsletter_issue;
@endphp

<div style="margin-bottom: 30px;">
<h1 style="color: #111827; font-size: 24px; font-weight: 800; margin: 0 0 8px 0;">{{ $content->title }}</h1>
@if($volume || $issue)
<p style="font-size: 14px; color: #6b7280; font-weight: 600; margin: 0;">
@if($volume)Volume {{ $volume }}@endif
@if($volume && $issue) &nbsp;•&nbsp; @endif
@if($issue)Issue {{ $issue }}@endif
</p>
@endif
</div>

@foreach($content->newsletterArticles as $article)
<div style="margin-bottom: 45px; border-bottom: 1px solid #e5e7eb; padding-bottom: 35px;">

<h3 style="color: #111827; font-size: 18px; font-weight: 700; margin: 0 0 14px 0; line-height: 1.4;">{{ $article->title }}</h3>

@if($article->image_path)
<div style="margin-bottom: 16px;">
<img src="{{ url('storage/' . $article->image_path) }}" alt="{{ $article->title }}" style="width: 100%; max-width: 100%; height: auto; border-radius: 8px; display: block;">
</div>
@endif

<div style="font-size: 15px; line-height: 1.7em; color: #4b5563; margin-bottom: 18px;">
{{ Str::limit($article->content, 300) }}
</div>

<a href="{{ config('app.frontend_url') }}/newsletters/{{ $content->id }}" style="background-color: #004D40; border-radius: 4px; color: #ffffff; display: inline-block; font-size: 13px; font-weight: 600; line-height: 36px; padding: 0 20px; text-align: center; text-decoration: none;">{{ __('Read More') }}</a>

</div>
@endforeach

@else

# A New {{ $contentType }} has been published!

Hello!

We are excited to share some new content with you.

@if($content->excerpt)
<p style="font-size: 16px; line-height: 1.5em; color: #374151;">{{ $content->excerpt }}</p>
@elseif($content->description)
<p style="font-size: 16px; line-height: 1.5em; color: #374151;">{{ $content->description }}</p>
@endif

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
