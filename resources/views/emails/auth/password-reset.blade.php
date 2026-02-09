<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>Réinitialisation du mot de passe</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@500;600&display=swap');
        .font-sans { font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; }
        .font-serif { font-family: "Playfair Display", Georgia, "Times New Roman", serif; }
        a { color: inherit; }
    </style>
</head>
<body class="font-sans" style="margin:0; padding:0; background:#f8fafc;">
    <!-- Preheader (hidden) -->
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent;">
        Réinitialisez votre mot de passe {{ $appName }} (lien valable {{ $expiresMinutes }} min)
    </div>

    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f8fafc; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:560px; width:100%; margin:0 auto;">
                    <!-- Brand header -->
                    <tr>
                        <td style="padding:0 16px 16px 16px;">
                            <div style="display:inline-flex; align-items:center; gap:10px;">
                                <div style="width:34px; height:34px; border-radius:10px; background:rgba(0,95,2,0.06); border:1px solid rgba(0,95,2,0.10); display:inline-block;">
                                    <div style="width:34px; height:34px; line-height:34px; text-align:center; color:#005F02; font-weight:700;">M</div>
                                </div>
                                <div>
                                    <div class="font-serif" style="font-size:18px; font-weight:600; letter-spacing:-0.2px; color:#002e01;">
                                        {{ $appName }}
                                    </div>
                                    <div style="font-size:12px; color:#64748b; margin-top:2px;">
                                        Réinitialisation du mot de passe
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
                                            Mot de passe oublié ?
                                        </div>
                                        <div style="margin-top:8px; font-size:13px; line-height:1.6; color:#475569;">
                                            Nous avons reçu une demande de réinitialisation pour le compte
                                            @if(!empty($toEmail))<span style="font-weight:600; color:#0f172a;">{{ $toEmail }}</span>@endif.
                                            Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.
                                        </div>
                                    </td>
                                </tr>

                                <!-- CTA -->
                                <tr>
                                    <td align="center" style="padding:14px 22px 6px 22px;">
                                        <a href="{{ $resetUrl }}"
                                           style="display:inline-block; background:#005F02; color:#ffffff; text-decoration:none; padding:12px 16px; border-radius:12px; font-size:13px; font-weight:600;">
                                            Réinitialiser mon mot de passe
                                        </a>
                                        <div style="margin-top:10px; font-size:12px; color:#64748b;">
                                            Lien valable <span style="font-weight:600; color:#0f172a;">{{ $expiresMinutes }} minute(s)</span>.
                                        </div>
                                    </td>
                                </tr>

                                <!-- Fallback link -->
                                <tr>
                                    <td style="padding:14px 22px 22px 22px;">
                                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:12px 12px;">
                                            <div style="font-size:12px; line-height:1.55; color:#334155;">
                                                Si le bouton ne fonctionne pas, copiez-collez ce lien dans votre navigateur :
                                            </div>
                                            <div style="margin-top:8px; font-size:12px; line-height:1.5; color:#0f172a; word-break:break-all;">
                                                {{ $resetUrl }}
                                            </div>
                                        </div>

                                        <div style="margin-top:12px; background:rgba(242,227,187,0.35); border:1px solid rgba(242,227,187,0.65); border-radius:14px; padding:12px 12px;">
                                            <div style="font-size:12px; line-height:1.55; color:#334155;">
                                                Si vous n’êtes pas à l’origine de cette demande, vous pouvez ignorer cet e-mail.
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
                                © {{ date('Y') }} {{ $appName }}. Tous droits réservés.
                                <br>
                                <span style="color:#cbd5e1;">Cet e-mail a été envoyé automatiquement, merci de ne pas répondre.</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

