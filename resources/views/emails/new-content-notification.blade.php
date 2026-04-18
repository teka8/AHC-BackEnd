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

@if($volume || $issue)
<table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom: 16px;">
<tr>
<td dir="rtl" align="left" style="text-align: left; padding: 0; width: 100%;">
<!-- By setting dir="rtl", align="left" binds to the logical left, which is physically right -->
<span dir="ltr" style="font-size: 10px; color: #9ca3af; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block;">@if($volume)Vol. {{ $volume }}@endif@if($volume && $issue) &nbsp;•&nbsp; @endif@if($issue)Issue {{ $issue }}@endif</span>
</td>
</tr>
</table>
@endif

@foreach($content->newsletterArticles as $article)
<div style="margin-bottom: 50px; border-bottom: 2px solid #e5e7eb; padding-bottom: 40px;">

@if($article->title)
<div style="margin-bottom: 12px; display: inline-block; background-color: #7AC943; padding: 4px 10px; border-radius: 4px;">
<span style="color: #ffffff; font-size: 14px; font-weight: 700; text-transform: uppercase;">{{ $article->title }}</span>
</div>
@endif

@if($article->image_path)
<div style="margin-bottom: 20px;">
<img src="{{ url('storage/' . $article->image_path) }}" alt="{{ $article->title }}" style="width: 100%; max-width: 100%; height: auto; border-radius: 8px; display: block;">
</div>
@endif

@if($article->subtitle)
<h3 style="color: #111827; font-size: 20px; font-weight: 800; margin: 0 0 16px 0; line-height: 1.3;">{{ $article->subtitle }}</h3>
@endif

<div style="font-size: 15px; line-height: 1.7em; color: #4b5563; margin-bottom: 22px;">
{{ Str::limit($article->content, 400) }}
</div>

<div style="text-align: right;">
<a href="{{ config('app.frontend_url') }}/newsletters/{{ $content->id }}" style="background-color: #004D40; border-radius: 4px; color: #ffffff; display: inline-block; font-size: 13px; font-weight: 600; line-height: 36px; padding: 0 20px; text-align: center; text-decoration: none;">{{ __('Read More') }}</a>
</div>

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
