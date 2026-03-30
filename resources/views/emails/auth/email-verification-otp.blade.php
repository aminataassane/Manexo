<!doctype html>
<html lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>Code de vérification — {{ $appName }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Mona+Sans:wght@400;500;600;700&display=swap');
        body { margin:0; padding:0; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
        table { border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0; }
        img { border:0; outline:none; text-decoration:none; -ms-interpolation-mode:bicubic; }
        a { color:inherit; text-decoration:none; }
    </style>
    <!--[if mso]><style>table,td,div,p,span{font-family:Arial,sans-serif!important;}</style><![endif]-->
</head>
<body style="margin:0; padding:0; background-color:#f4f7f5; width:100%;">

    {{-- Preheader --}}
    <div style="display:none; font-size:1px; line-height:1px; max-height:0; max-width:0; opacity:0; overflow:hidden; mso-hide:all;">
        Votre code de vérification {{ $appName }} : {{ $code }} — valable {{ $expiresMinutes }} min
        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
    </div>

    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f4f7f5;">
        <tr>
            <td align="center" style="padding:40px 16px;">

                <!--[if mso]><table role="presentation" cellpadding="0" cellspacing="0" width="560" align="center"><tr><td><![endif]-->
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:560px; margin:0 auto;">

                    {{-- Top accent bar --}}
                    <tr>
                        <td style="background-color:#005F02; height:6px; font-size:0; line-height:0; border-radius:16px 16px 0 0;">&nbsp;</td>
                    </tr>

                    {{-- Main card --}}
                    <tr>
                        <td style="background-color:#ffffff; border:1px solid #e5e9e6; border-top:none; border-radius:0 0 16px 16px;">

                            {{-- Logo + App name --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="padding:28px 32px 0 32px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="vertical-align:middle; padding-right:14px;">
                                                    @if(!empty($logoUrl))
                                                        <img src="{{ $logoUrl }}" alt="{{ $appName }}" width="38" height="38" style="width:38px; height:38px; border-radius:10px; object-fit:contain; display:block;">
                                                    @else
                                                        <table role="presentation" cellpadding="0" cellspacing="0"><tr><td style="width:38px; height:38px; border-radius:10px; background-color:#f0fdf4; text-align:center; line-height:38px; font-family:'Mona Sans','Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                                                            <span style="font-size:18px; font-weight:700; color:#005F02;">M</span>
                                                        </td></tr></table>
                                                    @endif
                                                </td>
                                                <td style="vertical-align:middle; font-family:'Mona Sans','Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                                                    <div style="font-size:19px; font-weight:700; color:#005F02; letter-spacing:-0.3px;">{{ $appName }}</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Divider --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr><td style="padding:20px 32px 0;"><div style="height:1px; background-color:#f0f0f0;"></div></td></tr>
                            </table>

                            {{-- Content --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="padding:28px 32px 0 32px; font-family:'Mona Sans','Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">

                                        {{-- Icon + Heading --}}
                                        <div style="font-size:23px; font-weight:700; color:#111827; letter-spacing:-0.4px; line-height:1.3;">
                                            Confirmez votre adresse e-mail
                                        </div>

                                        {{-- Body text --}}
                                        <div style="margin-top:16px; font-size:15px; line-height:1.7; color:#4b5563;">
                                            Bonjour,<br><br>
                                            Pour finaliser la vérification de votre adresse e-mail
                                            @if(!empty($toEmail))
                                                <strong style="color:#111827;">{{ $toEmail }}</strong>,
                                            @endif
                                            veuillez saisir le code ci-dessous sur la page de vérification.
                                        </div>

                                    </td>
                                </tr>
                            </table>

                            {{-- OTP Code block --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" style="padding:28px 32px 8px 32px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="background-color:#f0fdf4; border:2px solid #bbf7d0; border-radius:14px; padding:20px 36px; text-align:center;">
                                                    <div style="font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,'Liberation Mono','Courier New',monospace; font-size:34px; font-weight:700; letter-spacing:0.45em; padding-left:0.45em; color:#005F02;">
                                                        {{ $code }}
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <div style="margin-top:14px; font-family:'Mona Sans','Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; font-size:13px; color:#6b7280;">
                                            Ce code expire dans <strong style="color:#111827;">{{ $expiresMinutes }} minute{{ $expiresMinutes > 1 ? 's' : '' }}</strong>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            {{-- Security notice --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="padding:24px 32px 32px 32px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#fffbeb; border-radius:12px; border:1px solid #fef3c7;">
                                            <tr>
                                                <td style="padding:14px 16px; font-family:'Mona Sans','Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; font-size:13px; line-height:1.6; color:#92400e;">
                                                    <strong>Vous n'avez pas fait cette demande ?</strong><br>
                                                    Ignorez simplement cet e-mail. Votre compte est en sécurité, aucune action n'est requise de votre part.
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:28px 16px 0; font-family:'Mona Sans','Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" style="font-size:12px; line-height:1.6; color:#9ca3af;">
                                        &copy; {{ date('Y') }} {{ $appName }} &mdash; Tous droits réservés
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top:8px; font-size:11px; color:#d1d5db;">
                                        {{ __('emails.reply_hint_no_reply') }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
                <!--[if mso]></td></tr></table><![endif]-->

            </td>
        </tr>
    </table>

</body>
</html>
