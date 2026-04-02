<?php
    $fontSans = "-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif";
?>
<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title><?php echo e($subject); ?></title>
    <style>
        body { margin:0; padding:0; -webkit-text-size-adjust:100%; }
        table { border-collapse:collapse; mso-table-lspace:0; mso-table-rspace:0; }
        a { color:#4b5563; text-decoration:underline; }
    </style>
    <!--[if mso]><style>table,td,div,p,span{font-family:Arial,sans-serif!important;}</style><![endif]-->
</head>
<body style="margin:0; padding:0; background-color:#ffffff; width:100%;">

    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#ffffff;">
        <tr>
            <td align="center" style="padding:24px 20px 32px;">

                <!--[if mso]><table role="presentation" cellpadding="0" cellspacing="0" width="600" align="center"><tr><td><![endif]-->
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px; margin:0 auto;">

                    <tr>
                        <td style="padding:28px 28px 0; font-family:<?php echo e($fontSans); ?>;">

                            
                            <div style="font-size:14px; font-weight:500; color:#6b7280;">
                                <?php echo e($orgName); ?>

                            </div>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($ticketReference)): ?>
                                <div style="margin-top:6px; font-size:12px; color:#9ca3af; font-family:ui-monospace,'Cascadia Code','Segoe UI Mono',Consolas,monospace; letter-spacing:0.02em;">
                                    <?php echo e($ticketReference); ?>

                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <div style="margin-top:14px; font-size:15px; font-weight:700; color:#111827;">
                                <?php echo e($senderName); ?>

                            </div>
                        </td>
                    </tr>

                    
                    <tr>
                        <td style="padding:20px 28px 0;">
                            <div style="height:1px; background-color:#e5e7eb; line-height:0; font-size:0;">&nbsp;</div>
                        </td>
                    </tr>

                    
                    <tr>
                        <td style="padding:20px 28px 0; font-family:<?php echo e($fontSans); ?>; font-size:15px; line-height:1.7; color:#1f2937;">
                            <div style="white-space:pre-line;"><?php echo nl2br(e($body)); ?></div>
                        </td>
                    </tr>

                    
                    <tr>
                        <td style="padding:20px 28px 0;">
                            <div style="height:1px; background-color:#e5e7eb; line-height:0; font-size:0;">&nbsp;</div>
                        </td>
                    </tr>

                    
                    <tr>
                        <td style="padding:0 28px 24px; font-family:<?php echo e($fontSans); ?>;">
                            <a href="<?php echo e($ticketUrl); ?>" target="_blank" style="color:#4b5563; font-size:14px; font-weight:500; text-decoration:underline;">
                                <?php echo e(__('emails.view_conversation')); ?> &rarr;
                            </a>
                        </td>
                    </tr>

                    
                    <tr>
                        <td style="padding:0 28px 28px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" style="font-family:<?php echo e($fontSans); ?>; font-size:11px; line-height:1.65; color:#9ca3af; text-align:center;">
                                        <?php echo e(__('emails.ticket_notification_context', ['org' => $orgName])); ?>

                                    </td>
                                </tr>
                                <?php echo $__env->make('emails.partials.mail-reply-hint', [
                                    'mailReplyMode' => $mailReplyMode ?? 'use_link',
                                    'mailReplyHintAlign' => 'center',
                                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/emails/notifications/ticket-new.blade.php ENDPATH**/ ?>