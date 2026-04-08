<?php
    $statusLabels = [
        'submitted' => __('pages.forms.status_submitted'),
        'overdue'   => __('pages.forms.status_overdue'),
        'pending'   => __('pages.forms.status_pending'),
        'expired'   => __('pages.forms.status_expired'),
    ];
?>

<div class="w-full max-w-full min-w-0 mx-auto" wire:init="loadPage">

    
    <div class="page-header">
        <div class="min-w-0">
            <h1 class="page-title"><?php echo e(__('pages.forms.my_forms')); ?></h1>
            <p class="page-subtitle"><?php echo e(__('pages.forms.subtitle')); ?></p>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('form_success')): ?>
        <div class="flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800 mb-6">
            <iconify-icon icon="solar:check-circle-bold" width="18" class="text-emerald-500 shrink-0"></iconify-icon>
            <span><?php echo e(session('form_success')); ?></span>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $ready): ?>
    <div class="mb-6 sm:mb-8 space-y-3 animate-pulse">
        <div class="flex gap-2 overflow-hidden">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 5; $i++): ?>
                <div class="h-10 flex-1 min-w-[5rem] max-w-[8rem] rounded-lg bg-slate-200"></div>
            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="h-px bg-slate-200"></div>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden divide-y divide-slate-100">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 5; $i++): ?>
            <div class="flex items-center gap-4 px-5 py-4">
                <div class="h-2.5 w-2.5 rounded-full bg-slate-200 shrink-0"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-4 w-[66%] max-w-sm rounded bg-slate-200"></div>
                    <div class="h-3 w-40 rounded bg-slate-100"></div>
                </div>
                <div class="h-9 w-20 rounded-lg bg-slate-200 shrink-0 hidden sm:block"></div>
            </div>
        <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php else: ?>
    <?php $stats = $this->formStats; ?>

    
    <div class="mb-6 sm:mb-8">
        <div class="flex items-center gap-1 overflow-x-auto pb-px">
            <?php
                $tabs = [
                    ['key' => 'pending',   'label' => __('pages.forms.to_fill'),       'count' => $stats['pending'],   'urgent' => false],
                    ['key' => 'overdue',   'label' => __('pages.forms.stat_overdue'),   'count' => $stats['overdue'],   'urgent' => true],
                    ['key' => 'submitted', 'label' => __('pages.forms.submitted'),      'count' => $stats['submitted'], 'urgent' => false],
                    ['key' => 'expired',   'label' => __('pages.forms.stat_expired'),   'count' => $stats['expired'],   'urgent' => false],
                    ['key' => 'all',       'label' => __('pages.forms.all'),            'count' => $stats['total'],     'urgent' => false],
                ];
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <button
                    type="button"
                    wire:click="setTab('<?php echo e($t['key']); ?>')"
                    wire:loading.attr="disabled"
                    wire:target="setTab"
                    class="relative px-4 py-2.5 text-sm font-medium whitespace-nowrap transition-colors <?php echo e($tab === $t['key'] ? 'text-slate-900' : 'text-slate-500 hover:text-slate-700'); ?>"
                >
                    <?php echo e($t['label']); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t['count'] > 0): ?>
                        <span class="ml-1.5 text-xs tabular-nums <?php echo e($tab === $t['key'] ? ($t['urgent'] ? 'text-red-600 font-semibold' : 'text-slate-900 font-semibold') : ($t['urgent'] && $t['count'] > 0 ? 'text-red-500' : 'text-slate-400')); ?>"><?php echo e($t['count']); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === $t['key']): ?>
                        <span class="absolute bottom-0 left-4 right-4 h-0.5 rounded-full" style="background: var(--accent);"></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
        <div class="h-px bg-slate-200"></div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($tab, ['pending', 'all'], true) && $this->teamForms->isNotEmpty()): ?>
        <div class="mb-8">
            <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4"><?php echo e(__('pages.forms.team_forms_title')); ?></h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->teamForms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teamForm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <a href="<?php echo e($teamForm->slug ? route('forms.fill-team-by-slug', $teamForm->slug) : route('forms.fill-team', $teamForm)); ?>"
                       wire:navigate.hover
                       class="group flex items-center gap-3.5 rounded-xl border border-slate-200 bg-white px-4 py-3.5 hover:border-slate-300 hover:shadow-sm transition-all">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-500">
                            <iconify-icon icon="solar:users-group-rounded-bold" width="16"></iconify-icon>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-slate-900 truncate group-hover:text-[var(--accent)] transition-colors"><?php echo e($teamForm->name); ?></div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($teamForm->description): ?>
                                <div class="text-xs text-slate-500 truncate mt-0.5"><?php echo e($teamForm->description); ?></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <iconify-icon icon="solar:arrow-right-linear" width="16" class="text-slate-300 group-hover:text-[var(--accent)] shrink-0 transition-colors"></iconify-icon>
                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($tab, ['pending', 'all'], true) && $this->teamForms->isNotEmpty()): ?>
        <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4"><?php echo e(__('pages.forms.assigned_to_you')); ?></h2>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <?php
                $aStatus = $a->status instanceof \App\Enums\FormAssignmentStatus ? $a->status->value : (string) $a->status;
                $isActionable = !in_array($aStatus, ['submitted', 'expired']);
                $isOverdue = $aStatus === 'overdue';
                $isSubmitted = $aStatus === 'submitted';
                $isExpired = $aStatus === 'expired';
            ?>
            <div class="flex items-center gap-4 px-5 py-4 <?php echo e(!$loop->last ? 'border-b border-slate-100' : ''); ?> <?php echo e($isExpired ? 'opacity-50' : ''); ?> <?php echo e($isOverdue ? 'bg-red-50/30' : ''); ?>">
                
                <div class="shrink-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOverdue): ?>
                        <div class="h-2.5 w-2.5 rounded-full bg-red-500 ring-4 ring-red-50"></div>
                    <?php elseif($isSubmitted): ?>
                        <div class="h-2.5 w-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-50"></div>
                    <?php elseif($isExpired): ?>
                        <div class="h-2.5 w-2.5 rounded-full bg-slate-300"></div>
                    <?php else: ?>
                        <div class="h-2.5 w-2.5 rounded-full ring-4 ring-[var(--accent-soft)]" style="background: var(--accent);"></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div class="min-w-0 flex-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isActionable): ?>
                        <a href="<?php echo e(route('forms.fill', $a)); ?>" class="text-sm font-semibold text-slate-900 hover:text-[var(--accent)] transition-colors truncate block"><?php echo e($a->form?->name ?? '—'); ?></a>
                    <?php else: ?>
                        <span class="text-sm font-semibold <?php echo e($isExpired ? 'text-slate-400' : 'text-slate-900'); ?> truncate block"><?php echo e($a->form?->name ?? '—'); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="flex items-center gap-3 mt-1 text-xs text-slate-500">
                        <span><?php echo e($a->assignedBy?->name ?? '—'); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($a->due_date): ?>
                            <span class="<?php echo e($isOverdue ? 'text-red-600 font-medium' : ''); ?>"><?php echo e($a->due_date->format('d/m/Y')); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="shrink-0 hidden sm:block">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOverdue): ?>
                        <span class="text-xs font-medium text-red-600"><?php echo e($statusLabels['overdue']); ?></span>
                    <?php elseif($isSubmitted): ?>
                        <span class="text-xs font-medium text-emerald-600"><?php echo e($statusLabels['submitted']); ?></span>
                    <?php elseif($isExpired): ?>
                        <span class="text-xs text-slate-400"><?php echo e($statusLabels['expired']); ?></span>
                    <?php else: ?>
                        <span class="text-xs text-slate-500"><?php echo e($statusLabels['pending']); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isActionable): ?>
                    <a href="<?php echo e(route('forms.fill', $a)); ?>"
                       wire:navigate.hover
                       class="shrink-0 inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors text-white hover:opacity-90"
                       style="background: var(--accent);">
                        <?php echo e(__('pages.forms.fill')); ?>

                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="py-16 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400 mb-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'submitted'): ?>
                        <iconify-icon icon="solar:check-read-linear" width="24"></iconify-icon>
                    <?php elseif($tab === 'overdue'): ?>
                        <iconify-icon icon="solar:alarm-linear" width="24"></iconify-icon>
                    <?php elseif($tab === 'expired'): ?>
                        <iconify-icon icon="solar:lock-keyhole-linear" width="24"></iconify-icon>
                    <?php else: ?>
                        <iconify-icon icon="solar:clipboard-check-linear" width="24"></iconify-icon>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <p class="text-sm font-semibold text-slate-700"><?php echo e(__('pages.forms.no_forms')); ?></p>
                <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'pending'): ?>
                        <?php echo e(__('pages.forms.no_forms_pending')); ?>

                    <?php elseif($tab === 'submitted'): ?>
                        <?php echo e(__('pages.forms.no_forms_submitted')); ?>

                    <?php elseif($tab === 'overdue'): ?>
                        <?php echo e(__('pages.forms.no_forms_overdue')); ?>

                    <?php elseif($tab === 'expired'): ?>
                        <?php echo e(__('pages.forms.no_forms_expired')); ?>

                    <?php else: ?>
                        <?php echo e(__('pages.forms.no_forms_assigned')); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab !== 'all'): ?>
                    <button type="button" wire:click="setTab('all')" wire:loading.attr="disabled" wire:target="setTab" class="mt-4 text-xs font-medium text-slate-500 hover:text-slate-900 transition-colors underline underline-offset-2">
                        <?php echo e(__('pages.forms.see_all')); ?>

                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/livewire/user-forms/index.blade.php ENDPATH**/ ?>