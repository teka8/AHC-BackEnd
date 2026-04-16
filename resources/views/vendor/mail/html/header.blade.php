@props(['url'])
<tr>
    <td align="center">
        <table align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
                <td class="header"
                    style="background-color: #7AC943; padding: 25px 0; text-align: center; border-bottom: 5px solid #5A9E31; border-radius: 12px 12px 0 0;">
                    <a href="{{ config('app.frontend_url') ?? url('/') }}"
                        style="display: block; text-decoration: none; width: 100%;">
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                            style="margin-bottom: 20px; padding: 0 30px;">
                            <tr>
                                <td align="left" style="vertical-align: middle;">
                                    <img src="https://ahc.aau.edu.et/images/logo_white.png" alt="AHC" height="40"
                                        style="vertical-align: middle; margin-right: 15px; max-height: 40px;">
                                    <img src="https://ahc.aau.edu.et/images/partners/Addis_Ababa_University_logo.png"
                                        alt="AAU" height="40" style="vertical-align: middle; max-height: 40px;">
                                </td>
                                <td align="right" style="vertical-align: middle;">
                                    <img src="https://ahc.aau.edu.et/images/partners/mastercard_foundation_-_logo.png"
                                        alt="Mastercard Foundation" height="40"
                                        style="vertical-align: middle; max-height: 40px;">
                                </td>
                            </tr>
                        </table>
                        <div style="text-align: center;">
                            <span
                                style="color: #ffffff; font-size: 20px; font-weight: 800; letter-spacing: 2px; font-family: 'Urbanist', 'Inter', Helvetica, Arial, sans-serif; text-transform: uppercase;">AHC
                                - AAU</span>
                        </div>
                    </a>
                </td>
            </tr>
        </table>
    </td>
</tr>