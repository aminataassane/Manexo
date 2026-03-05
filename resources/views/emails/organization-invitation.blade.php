<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ __('invitations.email_subject', ['org' => $orgName]) }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap');
        .font-sans, .font-serif { font-family: "Mona Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; font-optical-sizing: auto; font-variation-settings: "wdth" 100; }
        a { color: inherit; }
    </style>
</head>
<body class="font-sans" style="margin:0; padding:0; background:#f8fafc;">
    <!-- Preheader (hidden) -->
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent;">
        {{ __('invitations.email_body', ['inviter' => $inviterName, 'org' => $orgName, 'role' => $roleName]) }}
    </div>

    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f8fafc; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:560px; width:100%; margin:0 auto;">
                    <!-- Brand header -->
                    <tr>
                        <td style="padding:0 16px 16px 16px;">
                            <div style="display:inline-flex; align-items:center; gap:10px;">
                                @if(!empty($logoUrl))
                                    <img src="{{ $logoUrl }}" alt="{{ $appName }}" style="width:34px; height:34px; border-radius:10px; object-fit:contain; display:block;" />
                                @else
                                    <div style="width:34px; height:34px; border-radius:10px; background:rgba(0,95,2,0.06); border:1px solid rgba(0,95,2,0.10); display:inline-block;">
                                        <div style="width:34px; height:34px; line-height:34px; text-align:center; color:#005F02; font-weight:700;">M</div>
                                    </div>
                                @endif
                                <div>
                                    <div class="font-serif" style="font-size:18px; font-weight:600; letter-spacing:-0.2px; color:#002e01;">
                                        {{ $appName }}
                                    </div>
                                    <div style="font-size:12px; color:#64748b; margin-top:2px;">
                                        {{ __('invitations.email_heading') }}
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Card -->
                    <tr>
                        <td style="padding:0 16px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#ffffff; border-radius:18px; border:1px solid #f1f5f9; box-shadow:0 20px 45px rgba(15,23,42,0.08); overflow:hidden;">
                                <tr>
                                    <td style="padding:22px 22px 10px 22px;">
                                        <div class="font-serif" style="font-size:22px; font-weight:600; color:#002e01; letter-spacing:-0.3px;">
                                            {{ __('invitations.email_heading') }}
                                        </div>
                                        <div style="margin-top:8px; font-size:13px; line-height:1.6; color:#475569;">
                                            <span style="font-weight:600; color:#0f172a;">{{ $inviterName }}</span>
                                            {{ __('invitations.email_body_text', ['org' => $orgName, 'role' => $roleName]) }}
                                        </div>
                                    </td>
                                </tr>

                                <!-- CTA -->
                                <tr>
                                    <td align="center" style="padding:14px 22px 6px 22px;">
                                        <a href="{{ $acceptUrl }}"
                                           style="display:inline-block; background:#005F02; color:#ffffff; text-decoration:none; padding:12px 16px; border-radius:12px; font-size:13px; font-weight:600;">
                                            {{ __('invitations.email_cta') }}
                                        </a>
                                        <div style="margin-top:10px; font-size:12px; color:#64748b;">
                                            {{ __('invitations.email_expires') }}
                                        </div>
                                    </td>
                                </tr>

                                <!-- Fallback link -->
                                <tr>
                                    <td style="padding:14px 22px 22px 22px;">
                                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:12px 12px;">
                                            <div style="font-size:12px; line-height:1.55; color:#334155;">
                                                {{ __('invitations.email_fallback') }}
                                            </div>
                                            <div style="margin-top:8px; font-size:12px; line-height:1.5; color:#0f172a; word-break:break-all;">
                                                {{ $acceptUrl }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:18px 16px 0 16px;">
                            <div style="font-size:11px; line-height:1.6; color:#94a3b8; text-align:center;">
                                &copy; {{ date('Y') }} {{ $appName }}. {{ __('invitations.email_rights') }}
                                <br>
                                <span style="color:#cbd5e1;">{{ __('invitations.email_auto') }}</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
