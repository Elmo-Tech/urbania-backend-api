@php
    $logo = $message->embed(public_path('images/email/urbania-logo.png'));
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
            .email-header { padding: 28px 24px !important; }
            .email-content { padding: 30px 24px 28px !important; }
            .brand-logo-cell { width: 76px !important; }
            .brand-logo { width: 76px !important; height: auto !important; }
            .brand-name { padding-left: 16px !important; font-size: 36px !important; line-height: 42px !important; }
            .message-body { font-size: 17px !important; line-height: 27px !important; }
            .signature-card { padding: 22px 20px !important; }
            .signature-title { font-size: 22px !important; }
            .signature-email { font-size: 16px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #eef0f4; color: #242424; font-family: Arial, Helvetica, sans-serif; -webkit-text-size-adjust: 100%;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#eef0f4" style="width: 100%; background-color: #eef0f4;">
        <tr>
            <td class="email-outer" align="center" style="padding: 36px 16px;">
                <!--[if mso]><table role="presentation" width="640" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" style="width: 100%; max-width: 640px; background-color: #ffffff; border: 1px solid #ffffff; border-radius: 26px; overflow: hidden; box-shadow: 0 16px 36px rgba(29, 34, 48, 0.16);">
                    <tr>
                        <td class="email-header" bgcolor="#f9edf0" style="padding: 28px 40px; border-radius: 25px 25px 0 0; background-color: #f9edf0; background-image: linear-gradient(148deg, transparent 76%, rgba(186, 0, 10, 0.95) 76%), linear-gradient(161deg, transparent 70%, rgba(234, 35, 53, 0.45) 70%), linear-gradient(120deg, #fffafa 20%, #f7e8ed 72%, #f1b5bf 100%);">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td class="brand-logo-cell" width="112" valign="middle"><img class="brand-logo" src="{{ $logo }}" width="112" height="140" alt="Logo Urbania" style="display: block; width: 112px; height: auto; border: 0;"></td>
                                    <td class="brand-name" valign="middle" style="padding-left: 22px; color: #080808; font-family: Arial, Helvetica, sans-serif; font-size: 48px; font-weight: 700; line-height: 56px;">Urbania</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 34px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                <tr>
                                    <td width="132" height="5" bgcolor="#f20821" style="width: 132px; height: 5px; border-radius: 5px; font-size: 0; line-height: 0;">&nbsp;</td>
                                    <td style="border-top: 1px solid #f20821; font-size: 0; line-height: 0;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-content" style="padding: 38px 40px 32px;">
                            <div id="message-body" class="message-body" style="white-space: pre-wrap; overflow-wrap: anywhere; word-wrap: break-word; font-size: 20px; line-height: 30px; mso-line-height-rule: at-least;">{!! str_replace(["\r\n", "\r", "\n"], '<br>', e($body)) !!}</div>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                <tr><td height="30" style="height: 30px; font-size: 0; line-height: 0;">&nbsp;</td></tr>
                                <tr>
                                    <td class="signature-card" bgcolor="#faf0f2" style="padding: 24px 26px; border-radius: 20px; background-color: #faf0f2; background-image: linear-gradient(115deg, #fff1f2, #f3edf2);">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                            <tr>
                                                <td width="5" bgcolor="#ef0620" style="width: 5px; border-radius: 5px; font-size: 0;">&nbsp;</td>
                                                <td style="padding: 0 0 0 22px;">
                                                    <p class="signature-title" style="margin: 0 0 12px; color: #a50c13; font-size: 25px; font-weight: 700; line-height: 32px;">Ufficio Assistenza</p>
                                                    <p class="signature-email" style="margin: 0; font-size: 19px; line-height: 28px; overflow-wrap: anywhere; word-break: break-word;"><span aria-hidden="true" style="color: #ed0a1b;">&#9993;&#65038;</span>&nbsp; <a href="mailto:{{ $supportEmail }}" style="color: #242424; text-decoration: none;">{{ $supportEmail }}</a></p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr><td height="46" style="height: 46px; font-size: 0; line-height: 0;">&nbsp;</td></tr>
                                <tr>
                                    <td style="padding-top: 26px; border-top: 1px solid #d8d8dd;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                            <tr>
                                                <td width="60" valign="middle" style="width: 60px; padding-right: 16px;">
                                                    <table role="presentation" width="52" cellpadding="0" cellspacing="0" border="0"><tr><td align="center" height="52" bgcolor="#fce9ec" style="height: 52px; border-radius: 50%; color: #cf0e1a; font-size: 28px; line-height: 52px;">&#9742;&#65038;</td></tr></table>
                                                </td>
                                                <td valign="middle" style="padding-left: 20px; border-left: 1px solid #ef0620; font-size: 14px; line-height: 21px; color: #414148;">Messaggio inviato tramite il sistema di assistenza Urbania.</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td height="22" bgcolor="#ce0715" style="height: 22px; border-radius: 0 0 25px 25px; background-color: #ce0715; background-image: linear-gradient(160deg, #d71020 0%, #f0353a 54%, #b6000c 55%, #d70815 100%); font-size: 0; line-height: 0;">&nbsp;</td></tr>
                </table>
                <!--[if mso]></td></tr></table><![endif]-->
            </td>
        </tr>
    </table>
</body>
</html>
