<!doctype html>
<html lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ $subject }}</title>
    <style>
        body { margin:0; padding:0; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
        table { border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0; }
        img { border:0; outline:none; text-decoration:none; -ms-interpolation-mode:bicubic; }
        a { color:#005F02; text-decoration:underline; text-underline-offset:2px; }
    </style>
    <!--[if mso]><style>table,td,div,p,span{font-family:Arial,sans-serif!important;}</style><![endif]-->
</head>
@php
    $fontSans = "-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif";
    $fontSerif = "Georgia,'Times New Roman',Times,serif";
@endphp
<body style="margin:0; padding:0; background-color:#ffffff; width:100%;">

    {{-- Preheader --}}
    <div style="display:none; font-size:1px; line-height:1px; max-height:0; max-width:0; opacity:0; overflow:hidden; mso-hide:all;">
        {{ $preheader ?? $bodyText }}
        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
    </div>

    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#ffffff;">
        <tr>
            <td align="center" style="padding:32px 20px 48px;">

                <!--[if mso]><table role="presentation" cellpadding="0" cellspacing="0" width="560" align="center"><tr><td><![endif]-->
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:560px; margin:0 auto;">

                    {{-- Fine brand rule (no card, no thick bar) --}}
                    <tr>
                        <td style="border-top:3px solid #005F02; font-size:0; line-height:0;">&nbsp;</td>
                    </tr>

                    {{-- En-tête : org --}}
                    <tr>
                        <td style="padding:24px 0 0; font-family:{{ $fontSans }};">
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    @if(!empty($logoUrl))
                                        <td style="vertical-align:middle; padding-right:12px;">
                                            <img src="{{ $logoUrl }}" alt="{{ $orgName }}" width="32" height="32" style="width:32px; height:32px; display:block; object-fit:contain;">
                                        </td>
                                    @endif
                                    <td style="vertical-align:middle;">
                                        <span style="font-size:12px; font-weight:600; color:#6b7280; letter-spacing:0.04em; text-transform:uppercase;">{{ $orgName }}</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Référence ticket --}}
                    <tr>
                        <td style="padding:20px 0 0; font-family:ui-monospace,'Cascadia Code','Segoe UI Mono',Consolas,monospace; font-size:12px; color:#9ca3af; letter-spacing:0.02em;">
                            {{ $ticketReference }}
                        </td>
                    </tr>

                    {{-- Titre statut --}}
                    <tr>
                        <td style="padding:12px 0 0; font-family:{{ $fontSerif }}; font-size:24px; font-weight:400; color:#111827; line-height:1.25;">
                            {{ $heading }}
                        </td>
                    </tr>

                    {{-- Corps --}}
                    <tr>
                        <td style="padding:16px 0 0; font-family:{{ $fontSans }}; font-size:15px; line-height:1.65; color:#374151;">
                            {{ $bodyText }}
                        </td>
                    </tr>

                    {{-- Sujet : typographie seule, pas de bloc gris --}}
                    <tr>
                        <td style="padding:28px 0 0; font-family:{{ $fontSans }};">
                            <div style="font-size:11px; font-weight:600; color:#9ca3af; letter-spacing:0.08em; text-transform:uppercase; margin-bottom:6px;">{{ __('Sujet') }}</div>
                            <div style="font-size:15px; font-weight:600; color:#111827; line-height:1.4; border-left:2px solid #e5e7eb; padding-left:14px;">
                                {{ $ticketSubject }}
                            </div>
                        </td>
                    </tr>

                    {{-- Lien discret (pas bouton plein) --}}
                    <tr>
                        <td style="padding:28px 0 0; border-top:1px solid #e5e7eb; font-family:{{ $fontSans }};">
                            <a href="{{ $ticketUrl }}" target="_blank" style="color:#005F02; font-size:14px; font-weight:500; text-decoration:underline;">
                                {{ __('Voir le ticket') }} &rarr;
                            </a>
                        </td>
                    </tr>

                    {{-- Pied --}}
                    <tr>
                        <td style="padding:32px 0 0; font-family:{{ $fontSans }};">
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="font-size:11px; line-height:1.6; color:#9ca3af;">
                                        &copy; {{ date('Y') }} {{ $orgName }}
                                    </td>
                                </tr>
                                @include('emails.partials.mail-reply-hint', ['mailReplyMode' => $mailReplyMode ?? 'use_link'])
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
