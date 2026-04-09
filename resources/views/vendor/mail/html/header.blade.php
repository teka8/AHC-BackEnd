@props(['url'])
<tr>
<td class="header" style="background-color: #7AC943; padding: 40px 0; text-align: center; border-bottom: 6px solid #5A9E31;">
<a href="{{ config('app.frontend_url') ?? url('/') }}" style="display: inline-block; text-decoration: none;">
    <div style="background-color: rgba(255, 255, 255, 0.25); border: 1px solid rgba(255, 255, 255, 0.4); padding: 20px 35px; border-radius: 16px; display: inline-block; margin-bottom: 20px;">
        <img src="https://ahc.aau.edu.et/images/ahc-logo.png" alt="AHC" height="65" style="vertical-align: middle; margin: 0 15px; max-height: 65px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
        <img src="https://ahc.aau.edu.et/images/partners/Addis_Ababa_University_logo.png" alt="AAU" height="65" style="vertical-align: middle; margin: 0 15px; max-height: 65px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
        <img src="https://ahc.aau.edu.et/images/partners/mastercard_foundation_-_logo.png" alt="Mastercard Foundation" height="65" style="vertical-align: middle; margin: 0 15px; max-height: 65px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
    </div>
    <div style="text-align: center;">
        <span style="color: #ffffff; font-size: 30px; font-weight: 800; letter-spacing: 3px; font-family: 'Urbanist', 'Inter', Helvetica, Arial, sans-serif; text-transform: uppercase; text-shadow: 0 3px 6px rgba(0,0,0,0.25);">AHC - AAU</span>
    </div>
</a>
</td>
</tr>
