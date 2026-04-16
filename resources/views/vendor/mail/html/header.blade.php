@props(['url', 'volume' => null, 'issue' => null])
<tr>
    <td align="center">
        <table align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
                <td class="header"
                    style="background-color: #7AC943; padding: 25px 0 15px 0; text-align: center; border-bottom: 5px solid #5A9E31; border-radius: 12px 12px 0 0;">
                    <a href="{{ config('app.frontend_url') ?? url('/') }}"
                        style="display: block; text-decoration: none; width: 100%;">
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                            style="margin-bottom: 12px; padding: 0 30px;">
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
                        <div style="padding: 0 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td align="left" style="vertical-align: bottom;">
                                        <span style="color: #ffffff; font-size: 14px; font-weight: 800; letter-spacing: 1px; font-family: 'Urbanist', 'Inter', Helvetica, Arial, sans-serif; text-transform: uppercase;">Africa Health Collaborative - Addis Ababa University</span>
                                    </td>
                                    @if($volume || $issue)
                                    <td align="right" style="vertical-align: bottom; padding-bottom: 2px;">
                                        <span style="color: rgba(255,255,255,0.85); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Inter', Helvetica, Arial, sans-serif;">
                                            @if($volume)Vol. {{ $volume }}@endif
                                            @if($volume && $issue) &nbsp;•&nbsp; @endif
                                            @if($issue)Issue {{ $issue }}@endif
                                        </span>
                                    </td>
                                    @endif
                                 </tr>
                            </table>
                        </div>
                    </a>
                </td>
            </tr>
        </table>
    </td>
</tr>