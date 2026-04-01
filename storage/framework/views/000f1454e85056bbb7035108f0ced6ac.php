
<div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->pendingFormsCount > 0): ?>
        <div class="rounded-xl sm:rounded-2xl bg-white p-4 sm:p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base sm:text-lg font-bold text-slate-900"><?php echo e(__('pages.dashboard.forms_to_fill')); ?></h3>
                <a href="<?php echo e(route('forms.index')); ?>" class="text-sm font-semibold hover:underline w-fit" style="color: var(--accent);" wire:navigate><?php echo e(__('pages.dashboard.see_all_forms')); ?></a>
            </div>
            <ul class="space-y-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->pendingFormAssignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <?php
                        $faStatus = $fa->status instanceof \App\Enums\FormAssignmentStatus ? $fa->status->value : (string) $fa->status;
                        $isOverdue = $faStatus === 'overdue';
                    ?>
                    <li>
                        <a href="<?php echo e(route('forms.fill', $fa)); ?>" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50/30 hover:bg-slate-50/60 transition-colors shadow-sm group block" wire:navigate>
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl <?php echo e($isOverdue ? 'bg-red-50 text-red-600' : 'bg-[var(--accent-soft)] text-[var(--accent)]'); ?>">
                                <iconify-icon icon="<?php echo e($isOverdue ? 'solar:alarm-bold-duotone' : 'solar:clipboard-text-bold-duotone'); ?>" width="20"></iconify-icon>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-900 truncate group-hover:text-[var(--accent)] transition-colors"><?php echo e($fa->form?->name ?? '—'); ?></p>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-0.5 text-xs text-slate-500">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOverdue): ?>
                                        <span class="font-semibold text-red-600"><?php echo e(__('pages.dashboard.past_due')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fa->due_date): ?>
                                        <span class="flex items-center gap-1">
                                            <iconify-icon icon="solar:calendar-linear" width="12"></iconify-icon>
                                            <?php echo e(__('pages.dashboard.due_date')); ?>: <?php echo e($fa->due_date->format('d/m/Y')); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium border shrink-0 <?php echo e($isOverdue ? 'bg-red-50 text-red-700 border-red-100' : 'bg-[var(--accent-soft)] text-[var(--accent)] border-[var(--accent)]/20'); ?>">
                                <?php echo e($isOverdue ? __('pages.dashboard.past_due') : __('pages.forms.status_pending')); ?>

                            </span>
                            <iconify-icon icon="solar:arrow-right-linear" width="18" class="text-slate-400 group-hover:text-[var(--accent)] transition-colors shrink-0"></iconify-icon>
                        </a>
                    </li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/livewire/dashboard/pending-forms-panel.blade.php ENDPATH**/ ?>