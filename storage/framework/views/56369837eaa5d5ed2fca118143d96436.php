<!doctype html>
<html lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>Réinitialisation du mot de passe — <?php echo e($appName); ?></title>
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

    
    <div style="display:none; font-size:1px; line-height:1px; max-height:0; max-width:0; opacity:0; overflow:hidden; mso-hide:all;">
        Réinitialisez votre mot de passe <?php echo e($appName); ?> — lien valable <?php echo e($expiresMinutes); ?> min
        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
    </div>

    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f4f7f5;">
        <tr>
            <td align="center" style="padding:40px 16px;">

                <!--[if mso]><table role="presentation" cellpadding="0" cellspacing="0" width="560" align="center"><tr><td><![endif]-->
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:560px; margin:0 auto;">

                    
                    <tr>
                        <td style="background-color:#005F02; height:6px; font-size:0; line-height:0; border-radius:16px 16px 0 0;">&nbsp;</td>
                    </tr>

                    
                    <tr>
                        <td style="background-color:#ffffff; border:1px solid #e5e9e6; border-top:none; border-radius:0 0 16px 16px;">

                            
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="padding:28px 32px 0 32px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="vertical-align:middle; padding-right:14px;">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($logoUrl)): ?>
                                                        <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($appName); ?>" width="38" height="38" style="width:38px; height:38px; border-radius:10px; object-fit:contain; display:block;">
                                                    <?php else: ?>
                                                        <table role="presentation" cellpadding="0" cellspacing="0"><tr><td style="width:38px; height:38px; border-radius:10px; background-color:#f0fdf4; text-align:center; line-height:38px; font-family:'Mona Sans','Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                                                            <span style="font-size:18px; font-weight:700; color:#005F02;">M</span>
                                                        </td></tr></table>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </td>
                                                <td style="vertical-align:middle; font-family:'Mona Sans','Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                                                    <div style="font-size:19px; font-weight:700; color:#005F02; letter-spacing:-0.3px;"><?php echo e($appName); ?></div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr><td style="padding:20px 32px 0;"><div style="height:1px; background-color:#f0f0f0;"></div></td></tr>
                            </table>

                            
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="padding:28px 32px 0 32px; font-family:'Mona Sans','Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">

                                        
                                        <div style="font-size:23px; font-weight:700; color:#111827; letter-spacing:-0.4px; line-height:1.3;">
                                            Réinitialisez votre mot de passe
                                        </div>

                                        
                                        <div style="margin-top:16px; font-size:15px; line-height:1.7; color:#4b5563;">
                                            Bonjour,<br><br>
                                            Nous avons reçu une demande de réinitialisation du mot de passe associé au compte
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($toEmail)): ?>
                                                <strong style="color:#111827;"><?php echo e($toEmail); ?></strong>.
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <br><br>
                                            Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe sécurisé.
                                        </div>

                                    </td>
                                </tr>
                            </table>

                            
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" style="padding:28px 32px 8px 32px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="border-radius:12px; background-color:#005F02;">
                                                    <a href="<?php echo e($resetUrl); ?>" target="_blank" style="display:inline-block; padding:14px 40px; font-family:'Mona Sans','Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; font-size:15px; font-weight:600; color:#ffffff; text-decoration:none; border-radius:12px; mso-padding-alt:0;">
                                                        <!--[if mso]><i style="mso-font-width:150%; mso-text-raise:22pt;">&nbsp;</i><![endif]-->
                                                        <span style="mso-text-raise:11pt;">Réinitialiser mon mot de passe</span>
                                                        <!--[if mso]><i style="mso-font-width:150%;">&nbsp;</i><![endif]-->
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                        <div style="margin-top:14px; font-family:'Mona Sans','Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; font-size:13px; color:#6b7280;">
                                            Ce lien expire dans <strong style="color:#111827;"><?php echo e($expiresMinutes); ?> minute<?php echo e($expiresMinutes > 1 ? 's' : ''); ?></strong>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="padding:20px 32px 0 32px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f9fafb; border-radius:12px; border:1px solid #f3f4f6;">
                                            <tr>
                                                <td style="padding:14px 16px; font-family:'Mona Sans','Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                                                    <div style="font-size:12px; color:#6b7280; line-height:1.5;">
                                                        Le bouton ne fonctionne pas ? Copiez ce lien dans votre navigateur :
                                                    </div>
                                                    <div style="margin-top:8px; font-size:12px; color:#005F02; word-break:break-all; line-height:1.5;">
                                                        <?php echo e($resetUrl); ?>

                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="padding:16px 32px 32px 32px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#fffbeb; border-radius:12px; border:1px solid #fef3c7;">
                                            <tr>
                                                <td style="padding:14px 16px; font-family:'Mona Sans','Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; font-size:13px; line-height:1.6; color:#92400e;">
                                                    <strong>Vous n'avez pas fait cette demande ?</strong><br>
                                                    Aucune inquiétude, votre mot de passe actuel reste inchangé. Ignorez simplement cet e-mail.
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    
                    <tr>
                        <td style="padding:28px 16px 0; font-family:'Mona Sans','Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" style="font-size:12px; line-height:1.6; color:#9ca3af;">
                                        &copy; <?php echo e(date('Y')); ?> <?php echo e($appName); ?> &mdash; Tous droits réservés
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top:8px; font-size:11px; color:#d1d5db;">
                                        <?php echo e(__('emails.reply_hint_no_reply')); ?>

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
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\emails\auth\password-reset.blade.php ENDPATH**/ ?>