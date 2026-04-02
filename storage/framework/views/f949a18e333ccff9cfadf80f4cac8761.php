<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; line-height: 1.5; }
        .header { padding: 20px 30px; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px; }
        .header h1 { font-size: 18px; font-weight: bold; color: #0f172a; margin-bottom: 4px; }
        .header p { font-size: 10px; color: #64748b; }
        .content { padding: 0 30px; }
        .stats-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .stats-table td { padding: 12px 16px; border: 1px solid #e2e8f0; }
        .stats-table .stat-label { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .stats-table .stat-value { font-size: 20px; font-weight: bold; color: #0f172a; }
        .section-title { font-size: 13px; font-weight: bold; color: #0f172a; margin: 20px 0 10px; padding-bottom: 6px; border-bottom: 1px solid #e2e8f0; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th { background: #f8fafc; padding: 8px 12px; text-align: left; font-size: 9px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; }
        .data-table td { padding: 7px 12px; border-bottom: 1px solid #f1f5f9; font-size: 10px; color: #334155; }
        .data-table tr:nth-child(even) td { background: #f8fafc; }
        .footer { margin-top: 30px; padding: 15px 30px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #94a3b8; text-align: center; }
        .header-logo { display: inline-block; vertical-align: middle; }
        .header-logo img { height: 32px; max-width: 120px; object-fit: contain; }
        .header-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
        .brand-badge { display: inline-block; background: #f1f5f9; border-radius: 4px; padding: 3px 8px; font-size: 8px; font-weight: 600; color: #64748b; letter-spacing: 0.5px; }
        .footer-brand { font-weight: 600; color: #475569; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width:100%;border:none;border-collapse:collapse;">
            <tr>
                <td style="border:none;padding:0;vertical-align:middle;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($logoBase64)): ?>
                        <img src="<?php echo e($logoBase64); ?>" style="height:32px;max-width:120px;margin-right:10px;vertical-align:middle;" alt="">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span style="font-size:18px;font-weight:bold;color:#0f172a;vertical-align:middle;"><?php echo e($orgName); ?></span>
                </td>
                <td style="border:none;padding:0;text-align:right;vertical-align:middle;">
                    <span class="brand-badge">Manexo</span>
                </td>
            </tr>
        </table>
        <h1 style="margin-top:6px;"><?php echo e(__('task_report.shared_report_title')); ?></h1>
        <p><?php echo e(__('task_report.period_label', ['from' => $from->format('d/m/Y'), 'to' => $to->format('d/m/Y')])); ?> &middot; <?php echo e(__('task_report.report_generated', ['date' => now()->format('d/m/Y H:i')])); ?></p>
    </div>

    <div class="content">
        
        <table class="stats-table">
            <tr>
                <td style="width: 33%;">
                    <div class="stat-label"><?php echo e(__('task_report.tickets_closed')); ?></div>
                    <div class="stat-value"><?php echo e($stats['closedTicketsCount'] ?? 0); ?></div>
                </td>
                <td style="width: 33%;">
                    <div class="stat-label"><?php echo e(__('task_report.top_category')); ?></div>
                    <div class="stat-value" style="font-size: 14px;"><?php echo e($stats['topCategoryClosed']['name'] ?? '—'); ?></div>
                </td>
                <td style="width: 33%;">
                    <div class="stat-label"><?php echo e(__('task_report.top_user')); ?></div>
                    <div class="stat-value" style="font-size: 14px;"><?php echo e($stats['topUserClosed']['name'] ?? '—'); ?></div>
                </td>
            </tr>
        </table>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($closedTickets) && $closedTickets->isNotEmpty()): ?>
            <div class="section-title"><?php echo e(__('task_report.closed_tickets')); ?> (<?php echo e($closedTickets->count()); ?>)</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 35%;"><?php echo e(__('task_report.ticket')); ?></th>
                        <th style="width: 18%;"><?php echo e(__('task_report.category')); ?></th>
                        <th style="width: 18%;"><?php echo e(__('task_report.closed_at')); ?></th>
                        <th style="width: 18%;"><?php echo e(__('task_report.closed_by')); ?></th>
                        <th style="width: 11%;"><?php echo e(__('task_report.checklist_progress')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $closedTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php
                            $done = (int) ($ticket->checklist_done_count ?? 0);
                            $total = (int) ($ticket->checklist_items_count ?? 0);
                        ?>
                        <tr>
                            <td><?php echo e(Str::limit($ticket->subject, 60)); ?></td>
                            <td><?php echo e($ticket->category?->name ?? '—'); ?></td>
                            <td><?php echo e($ticket->updated_at->format('d/m/Y H:i')); ?></td>
                            <td><?php echo e($ticket->closedByUser?->name ?? $ticket->assignees->first()?->name ?? $ticket->creator?->name ?? '—'); ?></td>
                            <td><?php echo e($total > 0 ? $done . '/' . $total : '—'); ?></td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="footer">
        <span class="footer-brand">Manexo</span> &middot; <?php echo e($orgName); ?> &middot; <?php echo e(__('task_report.report_generated', ['date' => now()->format('d/m/Y H:i')])); ?>

    </div>
</body>
</html>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\pdf\task-report.blade.php ENDPATH**/ ?>