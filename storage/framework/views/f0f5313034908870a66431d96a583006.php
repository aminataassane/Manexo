<div class="rounded-xl sm:rounded-2xl bg-white shadow-sm border border-slate-100 overflow-hidden min-w-0">
    <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-base sm:text-lg font-bold text-slate-900"><?php echo e(__('pages.dashboard.my_recent_tickets')); ?></h3>
        <a href="<?php echo e(route('tickets.index')); ?>" class="text-sm font-semibold hover:underline" style="color: var(--accent);" wire:navigate><?php echo e(__('pages.dashboard.see_all')); ?></a>
    </div>
    <div class="responsive-table-wrap">
        <table class="w-full text-left min-w-[500px] sm:min-w-0">
            <thead class="bg-slate-50/50 text-xs uppercase text-slate-500 font-semibold">
                <tr>
                    <th class="px-4 sm:px-6 py-3"><?php echo e(__('pages.dashboard.subject')); ?></th>
                    <th class="px-4 sm:px-6 py-3"><?php echo e(__('pages.dashboard.status')); ?></th>
                    <th class="px-4 sm:px-6 py-3 text-right"><?php echo e(__('pages.dashboard.last_activity')); ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_4 = true; $__currentLoopData = $this->myRecentTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_4 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <?php
                        $statusValue = is_object($t->status) ? $t->status->value : (string) $t->status;
                        $pill = $statusPill($statusValue);
                    ?>
                    <tr class="group hover:bg-slate-50/80 transition-colors cursor-pointer" onclick="window.location='<?php echo e(route('tickets.discussion', $t)); ?>'">
                        <td class="px-4 sm:px-6 py-3 sm:py-4">
                            <div class="min-w-0">
                                <span class="inline-block text-[11px] font-mono font-medium text-slate-400 tracking-tight"><?php echo e($t->shortReference()); ?></span>
                                <p class="mt-0.5 text-sm font-semibold text-slate-900 group-hover:text-[var(--accent)] transition-colors truncate max-w-[280px]"><?php echo e($t->subject); ?></p>
                            </div>
                        </td>
                        <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border <?php echo e($pill['bg']); ?> <?php echo e($pill['text']); ?> <?php echo e($pill['border']); ?>">
                                <?php echo e($statusLabel($statusValue)); ?>

                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-3 sm:py-4 text-right text-sm text-slate-500 whitespace-nowrap"><?php echo e($t->updated_at?->diffForHumans()); ?></td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_4): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr>
                        <td colspan="3" class="px-4 sm:px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-3">
                                    <iconify-icon icon="solar:ticket-linear" width="28" class="text-slate-300"></iconify-icon>
                                </div>
                                <p class="text-sm font-semibold text-slate-700"><?php echo e(__('pages.dashboard.no_tickets_yet')); ?></p>
                                <p class="text-xs text-slate-500 mt-1"><?php echo e(__('pages.dashboard.no_tickets_hint')); ?></p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\dashboard\my-recent-tickets-table.blade.php ENDPATH**/ ?>