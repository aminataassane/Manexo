<?php
    $mode = $mailReplyMode ?? 'use_link';
    $hintAlign = $mailReplyHintAlign ?? 'left';
?>
<tr>
    <td align="<?php echo e($hintAlign); ?>" style="padding-top:12px; font-size:11px; line-height:1.55; color:#6b7280; text-align:<?php echo e($hintAlign); ?>;">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'reply_email'): ?>
            <?php echo e(__('emails.reply_hint_email')); ?>

        <?php elseif($mode === 'no_reply'): ?>
            <?php echo e(__('emails.reply_hint_no_reply')); ?>

        <?php else: ?>
            <?php echo e(__('emails.reply_hint_use_link')); ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </td>
</tr>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\emails\partials\mail-reply-hint.blade.php ENDPATH**/ ?>