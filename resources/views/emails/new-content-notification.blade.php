<x-mail::message>
@php
$isNewsletter = $content instanceof \App\Models\Others && $content->resource_type === \App\Models\Others::TYPE_NEWSLETTER;
$contentType = $isNewsletter ? 'Newsletter' : Str::studly(class_basename($content));
@endphp
# A New {{ $contentType }} has been published!

Hello!

We are excited to share some new content with you.

@if($isNewsletter)
@foreach($content->newsletterArticles as $article)
<div style="margin-bottom: 50px; border-bottom: 1px solid #e5e7eb; padding-bottom: 30px;">
<div style="margin-bottom: 10px;">
<h3 style="color: #111827; font-size: 19px; font-weight: 700; margin: 0; display: inline; line-height: 1.4;">{{ $article->title }}</h3>
@if($article->volume || $article->issue_number)
<span style="font-size: 13px; color: #6b7280; font-weight: 600; margin-left: 10px; vertical-align: middle;">
@if($article->volume) Vol. {{ $article->volume }} @endif
@if($article->volume && $article->issue_number) • @endif
@if($article->issue_number) Issue {{ $article->issue_number }} @endif
</span>
@endif
</div>

@if($article->image_path)
<div style="margin-bottom: 20px;">
<img src="{{ url('storage/' . $article->image_path) }}" alt="{{ $article->title }}" style="width: 100%; max-width: 100%; height: auto; border-radius: 8px; display: block;">
</div>
@endif

<div style="font-size: 15px; line-height: 1.6em; color: #4b5563; margin-bottom: 15px;">
{{ Str::limit($article->content, 300) }}
</div>

<div style="margin-bottom: 10px;">
<a href="{{ config('app.frontend_url') }}/newsletters/{{ $content->id }}" style="background-color: #004D40; border-radius: 4px; color: #ffffff; display: inline-block; font-size: 13px; font-weight: 600; line-height: 36px; text-align: center; text-decoration: none; width: 110px;">{{ __('Read More') }}</a>
</div>
</div>
@endforeach
@else
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
