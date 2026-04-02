<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($orgName); ?> — <?php echo e(__('task_report.shared_report_title')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/2.3.0/iconify-icon.min.js"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="mx-auto max-w-5xl py-10 px-4 sm:px-6">

        
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white font-bold text-sm">
                    <?php echo e(mb_substr($orgName, 0, 1)); ?>

                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight"><?php echo e(__('task_report.shared_report_title')); ?></h1>
                    <p class="text-sm text-slate-500"><?php echo e($orgName); ?> &middot; <?php echo e(__('task_report.shared_report_subtitle')); ?></p>
                </div>
            </div>
            <p class="text-sm text-slate-500 mt-2">
                <?php echo e(__('task_report.period_label', ['from' => $from->format('d/m/Y'), 'to' => $to->format('d/m/Y')])); ?>

            </p>
        </div>

        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="rounded-2xl bg-slate-900 p-6 text-white shadow-lg">
                <p class="text-sm font-medium text-white/70"><?php echo e(__('task_report.tickets_closed')); ?></p>
                <h3 class="text-3xl font-bold mt-2"><?php echo e(number_format($stats['closedTicketsCount'] ?? 0, 0, ',', ' ')); ?></h3>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($stats['tasksTotal'] ?? 0) > 0): ?>
                    <p class="text-xs text-white/70 mt-1"><?php echo e(__('task_report.sub_tasks_also', ['count' => $stats['tasksTotal']])); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500"><?php echo e(__('task_report.top_category')); ?></p>
                <h3 class="text-xl font-bold text-slate-900 mt-1"><?php echo e($stats['topCategoryClosed']['name'] ?? '—'); ?></h3>
                <p class="text-xs text-slate-400 mt-1"><?php echo e($stats['topCategoryClosed']['count'] ?? 0); ?> <?php echo e(trans_choice('task_report.tickets_count', $stats['topCategoryClosed']['count'] ?? 0)); ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500"><?php echo e(__('task_report.top_user')); ?></p>
                <h3 class="text-xl font-bold text-slate-900 mt-1"><?php echo e($stats['topUserClosed']['name'] ?? '—'); ?></h3>
                <p class="text-xs text-slate-400 mt-1"><?php echo e($stats['topUserClosed']['count'] ?? 0); ?> <?php echo e(trans_choice('task_report.tickets_count', $stats['topUserClosed']['count'] ?? 0)); ?></p>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($closedTickets) && $closedTickets->isNotEmpty()): ?>
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden mb-8">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900"><?php echo e(__('task_report.closed_tickets')); ?></h3>
                    <p class="text-xs text-slate-500 mt-0.5"><?php echo e($closedTickets->count()); ?> <?php echo e(trans_choice('task_report.tickets_count', $closedTickets->count())); ?></p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500">
                            <tr>
                                <th class="px-6 py-3 text-left"><?php echo e(__('task_report.ticket')); ?></th>
                                <th class="px-6 py-3 text-left"><?php echo e(__('task_report.category')); ?></th>
                                <th class="px-6 py-3 text-left"><?php echo e(__('task_report.closed_at')); ?></th>
                                <th class="px-6 py-3 text-left"><?php echo e(__('task_report.closed_by')); ?></th>
                                <th class="px-6 py-3 text-left"><?php echo e(__('task_report.checklist_progress')); ?></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $closedTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <?php
                                    $done = (int) ($ticket->checklist_done_count ?? 0);
                                    $total = (int) ($ticket->checklist_items_count ?? 0);
                                ?>
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-6 py-3 text-sm font-medium text-slate-900"><?php echo e(Str::limit($ticket->subject, 50)); ?></td>
                                    <td class="px-6 py-3 text-sm text-slate-600"><?php echo e($ticket->category?->name ?? '—'); ?></td>
                                    <td class="px-6 py-3 text-sm text-slate-500"><?php echo e($ticket->updated_at->format('d/m/Y H:i')); ?></td>
                                    <td class="px-6 py-3 text-sm text-slate-600"><?php echo e($ticket->closedByUser?->name ?? $ticket->assignees->first()?->name ?? $ticket->creator?->name ?? '—'); ?></td>
                                    <td class="px-6 py-3 text-sm text-slate-600"><?php echo e($total > 0 ? $done . '/' . $total : '—'); ?></td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="text-center text-xs text-slate-400 mt-8">
            <p><?php echo e(__('task_report.shared_report_footer')); ?></p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\reports\shared-task-report.blade.php ENDPATH**/ ?>