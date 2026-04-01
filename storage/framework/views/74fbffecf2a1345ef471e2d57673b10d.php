<div class="rounded-xl sm:rounded-2xl bg-white shadow-sm border border-slate-100 overflow-hidden min-w-0">
    <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-100 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <h3 class="text-base sm:text-lg font-bold text-slate-900"><?php echo e(__('pages.dashboard.priority_tickets')); ?></h3>
        <a href="<?php echo e(route('tickets.index')); ?>" class="text-sm font-semibold hover:underline w-fit" style="color: var(--accent);"><?php echo e(__('pages.dashboard.see_all')); ?></a>
    </div>
    <div class="responsive-table-wrap">
        <table class="w-full text-left min-w-[600px] sm:min-w-0">
            <thead class="bg-slate-50/50 text-xs uppercase text-slate-500 font-semibold">
                <tr>
                    <th class="px-4 sm:px-6 py-3"><?php echo e(__('pages.dashboard.subject')); ?></th>
                    <th class="px-4 sm:px-6 py-3"><?php echo e(__('pages.dashboard.status')); ?></th>
                    <th class="px-4 sm:px-6 py-3"><?php echo e(__('pages.dashboard.priority')); ?></th>
                    <th class="px-4 sm:px-6 py-3 text-right"><?php echo e(__('pages.dashboard.last_activity')); ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->priorityTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <?php
                        $statusValue = is_object($t->status) ? $t->status->value : (string) $t->status;
                        $pill = $statusPill($statusValue);
                    ?>
                    <tr class="group hover:bg-slate-50/80 transition-colors cursor-pointer" onclick="window.location='<?php echo e(route('tickets.discussion', $t)); ?>'">
                        <td class="px-4 sm:px-6 py-3 sm:py-4">
                            <div class="min-w-0">
                                <span class="inline-block text-[11px] font-mono font-medium text-slate-400 tracking-tight"><?php echo e($t->shortReference()); ?></span>
                                <p class="mt-0.5 text-sm font-semibold text-slate-900 group-hover:text-[var(--accent)] transition-colors truncate"><?php echo e($t->subject); ?></p>
                                <p class="mt-0.5 text-xs text-slate-500 line-clamp-1 max-w-[200px] sm:max-w-[280px]" title="<?php echo e($t->description); ?>"><?php echo e($t->description); ?></p>
                            </div>
                        </td>
                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border <?php echo e($pill['bg']); ?> <?php echo e($pill['text']); ?> <?php echo e($pill['border']); ?>">
                                <?php echo e($statusLabel($statusValue)); ?>

                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-3 sm:py-4">
                            <div class="flex items-center gap-1.5">
                                <div class="h-2 w-2 shrink-0 rounded-full" style="background-color: <?php echo e($t->priority_color ?? '#cbd5e1'); ?>"></div>
                                <span class="text-sm text-slate-700"><?php echo e($t->priority_name ?? __('pages.dashboard.normal_priority')); ?></span>
                            </div>
                        </td>
                        <td class="px-4 sm:px-6 py-3 sm:py-4 text-right text-sm text-slate-500 whitespace-nowrap"><?php echo e($t->updated_at?->diffForHumans()); ?></td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr>
                        <td colspan="4" class="px-4 sm:px-6 py-8 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <iconify-icon icon="solar:ticket-linear" width="32" class="mb-2 opacity-50"></iconify-icon>
                                <p><?php echo e(__('pages.dashboard.no_priority_tickets')); ?></p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/livewire/dashboard/priority-tickets-table.blade.php ENDPATH**/ ?>