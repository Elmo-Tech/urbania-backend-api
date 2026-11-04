@php
    $logo = $message->embed(public_path('images/email/urbania-logo.png'));
    $supportIcon = $message->embed(public_path('images/email/support-icon.png'));
    $envelopeIcon = $message->embed(public_path('images/email/envelope-icon.png'));
    $supportEmail = config('mail.from.address');
@endphp
<!DOCTYPE html>
<html lang="it" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urbania | Assistenza</title>
    <!--[if mso]>
    <xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
    <![endif]-->
    <style>
        @media only screen and (max-width: 600px) {
            .email-outer { padding: 16px 8px !important; }
            .email-header { padding: 20px 22px !important; }
            .email-content { padding: 24px 22px !important; }
            .brand-logo-cell { width: 64px !important; }
            .brand-logo { width: 64px !important; height: auto !important; }
            .brand-name { padding-left: 16px !important; font-size: 30px !important; line-height: 36px !important; }
            .message-body { font-size: 16px !important; line-height: 25px !important; }
            .signature-card { padding: 18px 16px !important; }
            .signature-title { font-size: 18px !important; }
            .signature-email { font-size: 15px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #eef0f4; color: #242424; font-family: Arial, Helvetica, sans-serif; -webkit-text-size-adjust: 100%;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#eef0f4" style="width: 100%; background-color: #eef0f4;">
        <tr>
            <td class="email-outer" align="center" style="padding: 24px 16px;">
                <!--[if mso]><table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" style="width: 100%; max-width: 600px; background-color: #ffffff; border: 1px solid #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 20px rgba(29, 34, 48, 0.08);">
                    <tr>
                        <td class="email-header" bgcolor="#f9edf0" style="padding: 22px 32px; border-radius: 19px 19px 0 0; background-color: #f9edf0; background-image: radial-gradient(ellipse 70% 140% at 120% 180%, #ba000a 98.5%, transparent 100%), radial-gradient(ellipse 90% 160% at 110% 180%, rgba(234, 35, 53, 0.38) 98.5%, transparent 100%), linear-gradient(120deg, #fffafa 20%, #f7e8ed 72%, #f1b5bf 100%);">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td class="brand-logo-cell" width="80" valign="middle"><img class="brand-logo" src="{{ $logo }}" width="80" height="100" alt="Logo Urbania" style="display: block; width: 80px; height: auto; border: 0;"></td>
                                    <td class="brand-name" valign="middle" style="padding-left: 20px; color: #080808; font-family: Arial, Helvetica, sans-serif; font-size: 36px; font-weight: 700; line-height: 44px;">Urbania</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                <tr>
                                    <td width="100" height="4" bgcolor="#f20821" style="width: 100px; height: 4px; border-radius: 4px; font-size: 0; line-height: 0;">&nbsp;</td>
                                    <td style="border-top: 1px solid #f20821; font-size: 0; line-height: 0;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-content" style="padding: 28px 32px 24px;">
                            <div id="message-body" class="message-body" style="white-space: pre-wrap; overflow-wrap: anywhere; word-wrap: break-word; font-size: 16px; line-height: 25px; mso-line-height-rule: at-least;">{!! str_replace(["\r\n", "\r", "\n"], '<br>', e($body)) !!}</div>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                <tr><td height="24" style="height: 24px; font-size: 0; line-height: 0;">&nbsp;</td></tr>
                                <tr>
                                    <td class="signature-card" bgcolor="#faf0f2" style="padding: 18px 20px; border-radius: 14px; background-color: #faf0f2; background-image: linear-gradient(115deg, #fff1f2, #f3edf2);">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                            <tr>
                                                <td width="3" bgcolor="#ef0620" style="width: 3px; border-radius: 3px; font-size: 0;">&nbsp;</td>
                                                <td style="padding: 0 0 0 16px;">
                                                    <p class="signature-title" style="margin: 0 0 8px; color: #a50c13; font-size: 20px; font-weight: 700; line-height: 26px;">Ufficio Assistenza</p>
                                                    <p class="signature-email" style="margin: 0; font-size: 16px; line-height: 24px; overflow-wrap: anywhere; word-break: break-word;"><img src="{{ $envelopeIcon }}" width="16" height="16" alt="" style="display: inline-block; width: 16px; height: 16px; border: 0; vertical-align: middle;">&nbsp; <a href="mailto:{{ $supportEmail }}" style="color: #242424; text-decoration: none;">{{ $supportEmail }}</a></p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr><td height="28" style="height: 28px; font-size: 0; line-height: 0;">&nbsp;</td></tr>
                                <tr>
                                    <td style="padding-top: 20px; border-top: 1px solid #d8d8dd;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                            <tr>
                                                <td width="40" valign="middle" style="width: 40px; padding-right: 14px;">
                                                    <table role="presentation" width="40" cellpadding="0" cellspacing="0" border="0"><tr><td align="center" valign="middle" height="40" bgcolor="#faf0f2" style="height: 40px; border-radius: 10px;"><img src="{{ $supportIcon }}" width="24" height="24" alt="" style="display: block; width: 24px; height: 24px; border: 0;"></td></tr></table>
                                                </td>
                                                <td valign="middle" style="padding-left: 16px; border-left: 1px solid #e6d5d8; font-size: 13px; line-height: 20px; color: #62626a;">Messaggio inviato tramite il sistema di assistenza Urbania.</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td height="14" bgcolor="#ce0715" style="height: 14px; border-radius: 0 0 19px 19px; background-color: #ce0715; background-image: linear-gradient(160deg, #d71020 0%, #f0353a 54%, #b6000c 55%, #d70815 100%); font-size: 0; line-height: 0;">&nbsp;</td></tr>
                </table>
                <!--[if mso]></td></tr></table><![endif]-->
            </td>
        </tr>
    </table>
</body>
</html>
