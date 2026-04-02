<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1e293b; line-height: 1.5; }
        .header { padding: 20px 30px; border-bottom: 3px solid #0f172a; margin-bottom: 20px; }
        .header h1 { font-size: 18px; font-weight: bold; color: #0f172a; margin-bottom: 2px; }
        .header p { font-size: 9px; color: #64748b; }
        .content { padding: 0 30px; }

        .kpi-table { width: 100%; margin-bottom: 18px; border-collapse: collapse; }
        .kpi-table td { padding: 10px 14px; border: 1px solid #e2e8f0; vertical-align: top; }
        .kpi-label { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
        .kpi-value { font-size: 18px; font-weight: bold; color: #0f172a; margin-top: 2px; }
        .kpi-accent td:first-child { background: #0f172a; color: #fff; }
        .kpi-accent td:first-child .kpi-label { color: #94a3b8; }
        .kpi-accent td:first-child .kpi-value { color: #fff; }

        .section-title { font-size: 12px; font-weight: bold; color: #0f172a; margin: 18px 0 8px; padding-bottom: 5px; border-bottom: 1px solid #e2e8f0; }
        .section-title span { font-weight: normal; font-size: 10px; color: #64748b; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .data-table th { background: #f8fafc; padding: 6px 10px; text-align: left; font-size: 8px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; }
        .data-table td { padding: 5px 10px; border-bottom: 1px solid #f1f5f9; font-size: 9px; color: #334155; }
        .data-table tr:nth-child(even) td { background: #fafbfc; }

        .badge { display: inline-block; padding: 1px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; }
        .badge-red { background: #fef2f2; color: #dc2626; }
        .badge-amber { background: #fffbeb; color: #d97706; }
        .badge-blue { background: #eff6ff; color: #2563eb; }
        .badge-slate { background: #f1f5f9; color: #475569; }

        .backlog-table { width: 100%; margin-bottom: 18px; border-collapse: collapse; }
        .backlog-table td { padding: 8px 14px; border: 1px solid #e2e8f0; text-align: center; width: 33.33%; }
        .backlog-label { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
        .backlog-value { font-size: 16px; font-weight: bold; color: #0f172a; }

        .agent-table { width: 50%; }
        .two-col { width: 100%; }
        .two-col td { vertical-align: top; padding: 0; }
        .two-col td:first-child { padding-right: 12px; width: 55%; }
        .two-col td:last-child { padding-left: 12px; width: 45%; }

        .footer { margin-top: 20px; padding: 12px 30px; border-top: 1px solid #e2e8f0; font-size: 8px; color: #94a3b8; text-align: center; }
        .footer-brand { font-weight: 600; color: #475569; }
        .brand-badge { display: inline-block; background: #f1f5f9; border-radius: 4px; padding: 3px 8px; font-size: 8px; font-weight: 600; color: #64748b; letter-spacing: 0.5px; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: 'DejaVu Sans Mono', monospace; }
        .text-bold { font-weight: bold; }
        .text-red { color: #dc2626; }
        .text-amber { color: #d97706; }
    </style>
</head>
<body>
    
    <div class="header">
        <table style="width:100%;border:none;border-collapse:collapse;">
            <tr>
                <td style="border:none;padding:0;vertical-align:middle;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($logoBase64)): ?>
                        <img src="<?php echo e($logoBase64); ?>" style="height:30px;max-width:120px;margin-right:10px;vertical-align:middle;" alt="">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span style="font-size:18px;font-weight:bold;color:#0f172a;vertical-align:middle;"><?php echo e($orgName); ?></span>
                </td>
                <td style="border:none;padding:0;text-align:right;vertical-align:middle;">
                    <span class="brand-badge">Manexo</span>
                </td>
            </tr>
        </table>
        <h1 style="margin-top:6px;"><?php echo e(__('daily_report.title')); ?></h1>
        <p><?php echo e(__('daily_report.export_date')); ?> : <?php echo e(\Illuminate\Support\Carbon::parse($date)->translatedFormat('l j F Y')); ?> &middot; <?php echo e(__('task_report.report_generated', ['date' => now()->format('d/m/Y H:i')])); ?></p>
    </div>

    <div class="content">
        
        <table class="kpi-table kpi-accent">
            <tr>
                <td style="width:25%">
                    <div class="kpi-label"><?php echo e(__('daily_report.created_today')); ?></div>
                    <div class="kpi-value"><?php echo e($summary['created_total']); ?></div>
                </td>
                <td style="width:25%">
                    <div class="kpi-label"><?php echo e(__('daily_report.resolved_today')); ?></div>
                    <div class="kpi-value"><?php echo e($summary['resolved_today']); ?></div>
                </td>
                <td style="width:25%">
                    <div class="kpi-label"><?php echo e(__('daily_report.avg_first_response')); ?></div>
                    <div class="kpi-value" style="font-size:14px"><?php echo e($formatDuration($summary['avg_first_response_seconds'])); ?></div>
                </td>
                <td style="width:25%">
                    <div class="kpi-label"><?php echo e(__('daily_report.avg_resolution_time')); ?></div>
                    <div class="kpi-value" style="font-size:14px"><?php echo e($formatDuration($summary['avg_resolution_seconds'])); ?></div>
                </td>
            </tr>
        </table>

        
        <table class="backlog-table">
            <tr>
                <td>
                    <div class="backlog-label"><?php echo e(__('daily_report.open')); ?></div>
                    <div class="backlog-value"><?php echo e($summary['backlog_open']); ?></div>
                </td>
                <td>
                    <div class="backlog-label"><?php echo e(__('daily_report.in_progress')); ?></div>
                    <div class="backlog-value"><?php echo e($summary['backlog_in_progress']); ?></div>
                </td>
                <td>
                    <div class="backlog-label"><?php echo e(__('daily_report.pending')); ?></div>
                    <div class="backlog-value"><?php echo e($summary['backlog_pending']); ?></div>
                </td>
            </tr>
        </table>

        
        <?php $riskCount = $atRisk['overdue']->count() + $atRisk['due_soon']->count(); ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($riskCount > 0): ?>
            <div class="section-title"><?php echo e(__('daily_report.at_risk')); ?> <span>(<?php echo e($riskCount); ?>)</span></div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:10%"><?php echo e(__('daily_report.status')); ?></th>
                        <th style="width:10%"><?php echo e(__('daily_report.reference')); ?></th>
                        <th style="width:25%"><?php echo e(__('daily_report.subject')); ?></th>
                        <th style="width:12%"><?php echo e(__('daily_report.group')); ?></th>
                        <th style="width:10%"><?php echo e(__('daily_report.priority')); ?></th>
                        <th style="width:18%"><?php echo e(__('daily_report.assignees')); ?></th>
                        <th style="width:10%"><?php echo e(__('daily_report.due_date')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $atRisk['overdue']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <tr>
                            <td><span class="badge badge-red"><?php echo e(__('daily_report.overdue')); ?></span></td>
                            <td class="font-mono"><?php echo e($ticket->public_id); ?></td>
                            <td class="text-bold"><?php echo e(Str::limit($ticket->subject, 50)); ?></td>
                            <td><?php echo e($ticket->group?->name ?? '—'); ?></td>
                            <td><?php echo e($ticket->priority?->name ?? '—'); ?></td>
                            <td><?php echo e($ticket->assignees->pluck('name')->join(', ') ?: __('daily_report.no_assignee')); ?></td>
                            <td class="text-bold text-red"><?php echo e($ticket->due_date?->format('d/m/Y')); ?></td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $atRisk['due_soon']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <tr>
                            <td><span class="badge badge-amber"><?php echo e(__('daily_report.due_soon')); ?></span></td>
                            <td class="font-mono"><?php echo e($ticket->public_id); ?></td>
                            <td class="text-bold"><?php echo e(Str::limit($ticket->subject, 50)); ?></td>
                            <td><?php echo e($ticket->group?->name ?? '—'); ?></td>
                            <td><?php echo e($ticket->priority?->name ?? '—'); ?></td>
                            <td><?php echo e($ticket->assignees->pluck('name')->join(', ') ?: __('daily_report.no_assignee')); ?></td>
                            <td class="text-bold text-amber"><?php echo e($ticket->due_date?->format('d/m/Y')); ?></td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTickets->isNotEmpty()): ?>
            <div class="section-title"><?php echo e(__('daily_report.active_tickets')); ?> <span>(<?php echo e($activeTickets->count()); ?>)</span></div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:9%"><?php echo e(__('daily_report.reference')); ?></th>
                        <th style="width:25%"><?php echo e(__('daily_report.subject')); ?></th>
                        <th style="width:11%"><?php echo e(__('daily_report.group')); ?></th>
                        <th style="width:9%"><?php echo e(__('daily_report.priority')); ?></th>
                        <th style="width:16%"><?php echo e(__('daily_report.assignees')); ?></th>
                        <th style="width:11%"><?php echo e(__('daily_report.updated_at')); ?></th>
                        <th style="width:9%"><?php echo e(__('daily_report.due_date')); ?></th>
                        <th style="width:10%"><?php echo e(__('daily_report.status')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $activeTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php
                            $statusBadge = match($ticket->status) {
                                \App\Enums\TicketStatus::Open => 'badge-blue',
                                \App\Enums\TicketStatus::InProgress => 'badge-amber',
                                \App\Enums\TicketStatus::Pending => 'badge-slate',
                                default => 'badge-slate',
                            };
                            $statusLabel = match($ticket->status) {
                                \App\Enums\TicketStatus::Open => __('daily_report.open'),
                                \App\Enums\TicketStatus::InProgress => __('daily_report.in_progress'),
                                \App\Enums\TicketStatus::Pending => __('daily_report.pending'),
                                default => $ticket->status->value,
                            };
                        ?>
                        <tr>
                            <td class="font-mono"><?php echo e($ticket->public_id); ?></td>
                            <td class="text-bold"><?php echo e(Str::limit($ticket->subject, 50)); ?></td>
                            <td><?php echo e($ticket->group?->name ?? '—'); ?></td>
                            <td><?php echo e($ticket->priority?->name ?? '—'); ?></td>
                            <td><?php echo e($ticket->assignees->pluck('name')->join(', ') ?: __('daily_report.no_assignee')); ?></td>
                            <td><?php echo e($ticket->updated_at?->format('d/m/Y H:i')); ?></td>
                            <td><?php echo e($ticket->due_date?->format('d/m/Y') ?? '—'); ?></td>
                            <td><span class="badge <?php echo e($statusBadge); ?>"><?php echo e($statusLabel); ?></span></td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($agentPerf)): ?>
            <div class="section-title"><?php echo e(__('daily_report.agent_performance')); ?></div>
            <table class="data-table agent-table">
                <thead>
                    <tr>
                        <th style="width:50%"><?php echo e(__('daily_report.agent')); ?></th>
                        <th style="width:25%" class="text-center"><?php echo e(__('daily_report.handled')); ?></th>
                        <th style="width:25%" class="text-center"><?php echo e(__('daily_report.resolved')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $agentPerf; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <tr>
                            <td class="text-bold"><?php echo e($agent['name']); ?></td>
                            <td class="text-center"><?php echo e($agent['handled']); ?></td>
                            <td class="text-center"><?php echo e($agent['resolved']); ?></td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="footer">
        <span class="footer-brand">Manexo</span> &middot; <?php echo e($orgName); ?> &middot; <?php echo e(__('daily_report.title')); ?> &middot; <?php echo e(\Illuminate\Support\Carbon::parse($date)->format('d/m/Y')); ?> &middot; <?php echo e(__('task_report.report_generated', ['date' => now()->format('d/m/Y H:i')])); ?>

    </div>
</body>
</html>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\pdf\daily-report.blade.php ENDPATH**/ ?>