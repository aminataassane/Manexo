<!doctype html>
<html lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ __('invitations.accepted_email_subject', ['name' => $acceptedByName, 'org' => $organizationName]) }}</title>
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
        {{ __('invitations.accepted_email_preheader', ['name' => $acceptedByName, 'org' => $organizationName, 'role' => $roleName]) }}
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

                                        {{-- Heading --}}
                                        <div style="font-size:23px; font-weight:700; color:#111827; letter-spacing:-0.4px; line-height:1.3;">
                                            {{ __('invitations.accepted_email_heading') }}
                                        </div>

                                        {{-- Body text --}}
                                        <div style="margin-top:16px; font-size:15px; line-height:1.7; color:#4b5563;">
                                            Bonjour,<br><br>
                                            <strong style="color:#111827;">{{ $acceptedByName }}</strong>
                                            {{ __('invitations.accepted_email_body', ['org' => $organizationName, 'role' => $roleName]) }}
                                        </div>

                                    </td>
                                </tr>
                            </table>

                            {{-- Member info box --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="padding:20px 32px 0 32px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f0fdf4; border-radius:12px; border:1px solid #bbf7d0;">
                                            <tr>
                                                <td style="padding:16px 20px; font-family:'Mona Sans','Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                                        <tr>
                                                            <td style="font-size:13px; color:#6b7280;">{{ __('invitations.accepted_email_member_label') }}</td>
                                                            <td align="right" style="font-size:14px; font-weight:600; color:#111827;">{{ $acceptedByName }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" style="padding-top:4px;"><div style="height:1px; background-color:#dcfce7;"></div></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding-top:4px; font-size:13px; color:#6b7280;">{{ __('invitations.accepted_email_email_label') }}</td>
                                                            <td align="right" style="padding-top:4px; font-size:14px; font-weight:500; color:#4b5563;">{{ $acceptedByEmail }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" style="padding-top:4px;"><div style="height:1px; background-color:#dcfce7;"></div></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding-top:4px; font-size:13px; color:#6b7280;">{{ __('invitations.email_org_label') }}</td>
                                                            <td align="right" style="padding-top:4px; font-size:14px; font-weight:600; color:#111827;">{{ $organizationName }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" style="padding-top:4px;"><div style="height:1px; background-color:#dcfce7;"></div></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding-top:4px; font-size:13px; color:#6b7280;">{{ __('invitations.email_role_label') }}</td>
                                                            <td align="right" style="padding-top:4px; font-size:14px; font-weight:600; color:#005F02;">{{ $roleName }}</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- CTA Button --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" style="padding:28px 32px 32px 32px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="border-radius:12px; background-color:#005F02;">
                                                    <a href="{{ $dashboardUrl }}" target="_blank" style="display:inline-block; padding:14px 40px; font-family:'Mona Sans','Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; font-size:15px; font-weight:600; color:#ffffff; text-decoration:none; border-radius:12px; mso-padding-alt:0;">
                                                        <!--[if mso]><i style="mso-font-width:150%; mso-text-raise:22pt;">&nbsp;</i><![endif]-->
                                                        <span style="mso-text-raise:11pt;">{{ __('invitations.accepted_email_cta') }}</span>
                                                        <!--[if mso]><i style="mso-font-width:150%;">&nbsp;</i><![endif]-->
                                                    </a>
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
                                        &copy; {{ date('Y') }} {{ $appName }} &mdash; {{ __('invitations.email_rights') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top:8px; font-size:11px; color:#d1d5db;">
                                        {{ __('invitations.email_auto') }}
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
