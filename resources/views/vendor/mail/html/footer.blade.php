<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center" style="padding: 32px 0;">
    {{ Illuminate\Mail\Markdown::parse($slot) }}

    <div style="text-align: center; margin-top: 30px;">
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

        <p style="color: #6b7280; font-size: 13px; margin-bottom: 15px;">
            <strong>{{ config('settings.company_name', 'Africa Health Collaborative - AAU') }}</strong><br>
            {{ config('settings.company_address', 'Addis Ababa University') }}
        </p>
        
        <p style="margin-top: 15px;">
            <a href="{{ rtrim(config('app.frontend_url') ?? url('/'), '/') }}" style="color: #003B5C; text-decoration: none; font-size: 13px; font-weight: 500;">{{ str_replace(['https://', 'http://'], '', config('app.frontend_url') ?? 'ahc.aau.edu.et') }}</a>
        </p>
        
        <p style="margin-top: 25px; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 15px;">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</td>
</tr>
</table>
</td>
</tr>
