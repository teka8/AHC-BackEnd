<x-mail::message>
<x-slot:header>
    <tr>
        <td class="header" style="background-color: #003B5C; padding: 25px 0; text-align: center; border-bottom: 4px solid #F2A900;">
            <a href="{{ config('app.frontend_url') }}" style="display: inline-block;">
                <div style="background-color: #ffffff; padding: 15px 25px; border-radius: 8px; display: inline-block; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <img src="{{ config('app.frontend_url') }}/images/ahc-logo.png" alt="AHC" height="60" style="vertical-align: middle; margin: 0 12px; max-height: 60px;">
                    <img src="{{ config('app.frontend_url') }}/partners/Addis_Ababa_University_logo.png" alt="AAU" height="60" style="vertical-align: middle; margin: 0 12px; max-height: 60px;">
                    <img src="{{ config('app.frontend_url') }}/partners/mastercard_foundation_-_logo.png" alt="Mastercard Foundation" height="60" style="vertical-align: middle; margin: 0 12px; max-height: 60px;">
                </div>
            </a>
        </td>
    </tr>
</x-slot:header>

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

<x-slot:footer>
    <x-mail::footer>
        <div style="text-align: center; padding: 20px 0;">
            <p style="margin-bottom: 12px; font-weight: 600; color: #374151; font-size: 16px;">Connect with us</p>
            <div style="margin-bottom: 25px;">
                @if(config('settings.social_facebook'))
                    <a href="{{ config('settings.social_facebook') }}" style="display:inline-block; margin: 0 10px; color: #003B5C; text-decoration: none; font-weight: bold; font-size:14px;">Facebook</a>
                @endif
                @if(config('settings.social_twitter'))
                    <a href="{{ config('settings.social_twitter') }}" style="display:inline-block; margin: 0 10px; color: #003B5C; text-decoration: none; font-weight: bold; font-size:14px;">Twitter / X</a>
                @endif
                @if(config('settings.social_linkedin'))
                    <a href="{{ config('settings.social_linkedin') }}" style="display:inline-block; margin: 0 10px; color: #003B5C; text-decoration: none; font-weight: bold; font-size:14px;">LinkedIn</a>
                @endif
                @if(config('settings.social_youtube'))
                    <a href="{{ config('settings.social_youtube') }}" style="display:inline-block; margin: 0 10px; color: #003B5C; text-decoration: none; font-weight: bold; font-size:14px;">YouTube</a>
                @endif
                @if(config('settings.social_instagram'))
                    <a href="{{ config('settings.social_instagram') }}" style="display:inline-block; margin: 0 10px; color: #003B5C; text-decoration: none; font-weight: bold; font-size:14px;">Instagram</a>
                @endif
            </div>

            <p style="color: #6b7280; font-size: 13px; line-height: 1.6; margin-bottom: 15px;">
                You are receiving this email because you subscribed to AHC's updates.
                <br>
                Received this from a friend? <a href="{{ config('app.frontend_url') }}" style="color: #003B5C; font-weight: 500; text-decoration: underline;">Join our mailing list</a>.
            </p>

            <p style="color: #6b7280; font-size: 13px; margin-bottom: 15px;">
                <strong>{{ config('settings.company_name', 'Africa Health Collaborative - AAU') }}</strong><br>
                {{ config('settings.company_address', 'Addis Ababa University') }}
            </p>
            
            <p style="margin-top: 15px;">
                <a href="{{ config('app.frontend_url') }}" style="color: #003B5C; text-decoration: none; font-size: 13px; font-weight: 500;">{{ str_replace(['https://', 'http://'], '', config('app.frontend_url') ?? 'ahc.aau.edu.et') }}</a>
            </p>
            
            @if(! empty($unsubscribeUrl))
            <p style="margin-top: 25px; font-size: 12px; border-top: 1px solid #e5e7eb; padding-top: 15px;">
                <a href="{{ $unsubscribeUrl }}" style="color: #9ca3af; text-decoration: underline;">Unsubscribe at any time</a>
            </p>
            @endif
        </div>
    </x-mail::footer>
</x-slot:footer>
</x-mail::message>
