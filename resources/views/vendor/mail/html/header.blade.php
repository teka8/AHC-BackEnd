@props(['url'])
<tr>
<td class="header" style="background-color: #003B5C; padding: 25px 0; text-align: center; border-bottom: 4px solid #F2A900;">
<a href="{{ config('app.frontend_url') ?? url('/') }}" style="display: inline-block;">
    <div style="background-color: #ffffff; padding: 15px 25px; border-radius: 8px; display: inline-block; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <img src="{{ rtrim(config('app.frontend_url') ?? url('/'), '/') }}/images/ahc-logo.png" alt="AHC" height="60" style="vertical-align: middle; margin: 0 12px; max-height: 60px;">
        <img src="{{ rtrim(config('app.frontend_url') ?? url('/'), '/') }}/partners/Addis_Ababa_University_logo.png" alt="AAU" height="60" style="vertical-align: middle; margin: 0 12px; max-height: 60px;">
        <img src="{{ rtrim(config('app.frontend_url') ?? url('/'), '/') }}/partners/mastercard_foundation_-_logo.png" alt="Mastercard Foundation" height="60" style="vertical-align: middle; margin: 0 12px; max-height: 60px;">
    </div>
</a>
</td>
</tr>
