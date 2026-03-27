<?php
    $statusLabels = [
        'submitted' => __('pages.forms.status_submitted'),
        'overdue'   => __('pages.forms.status_overdue'),
        'pending'   => __('pages.forms.status_pending'),
        'expired'   => __('pages.forms.status_expired'),
    ];
?>

<div class="w-full max-w-full min-w-0 mx-auto">
    
    <div class="page-header">
        <div class="min-w-0 flex items-center gap-3">
            <div class="flex h-10 w-10 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-xl shadow-sm" style="background: var(--accent-soft); color: var(--accent);">
                <iconify-icon icon="solar:clipboard-text-bold-duotone" width="24" class="sm:w-7 sm:h-7"></iconify-icon>
            </div>
            <div class="min-w-0">
                <h1 class="page-title"><?php echo e(__('pages.forms.my_forms')); ?></h1>
                <p class="page-subtitle !mt-0.5"><?php echo e(__('pages.forms.subtitle')); ?></p>
            </div>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('form_success')): ?>
        <div class="rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800 flex items-center gap-3 shadow-sm mb-6 sm:mb-8" style="border: 1px solid #a7f3d0;">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                <iconify-icon icon="solar:check-circle-bold" width="18"></iconify-icon>
            </div>
            <span><?php echo e(session('form_success')); ?></span>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($loadStage >= 2): ?>
    
    <?php $stats = $this->formStats; ?>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-8 sm:mb-10">
        
        <button type="button" wire:click="$set('tab', 'pending')" class="stat-card text-left cursor-pointer group">
            <div class="flex items-center gap-3">
                <div class="stat-card-icon bg-[var(--accent-soft)] text-[var(--accent)]">
                    <iconify-icon icon="solar:clipboard-text-bold-duotone" width="20"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <div class="stat-card-value !mt-0"><?php echo e($stats['pending']); ?></div>
                    <span class="stat-card-label !mt-0"><?php echo e(__('pages.forms.stat_to_fill')); ?></span>
                </div>
            </div>
        </button>
        
        <button type="button" wire:click="$set('tab', 'overdue')" class="stat-card text-left cursor-pointer group"
            style="<?php echo e($stats['overdue'] > 0 ? 'border-color: #fecaca; background: rgba(254, 242, 242, 0.5);' : ''); ?>">
            <div class="flex items-center gap-3">
                <div class="stat-card-icon bg-red-100 text-red-600">
                    <iconify-icon icon="solar:alarm-bold-duotone" width="20"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <div class="stat-card-value !mt-0 <?php echo e($stats['overdue'] > 0 ? '!text-red-700' : ''); ?>"><?php echo e($stats['overdue']); ?></div>
                    <span class="stat-card-label !mt-0"><?php echo e(__('pages.forms.stat_overdue')); ?></span>
                </div>
            </div>
        </button>
        
        <button type="button" wire:click="$set('tab', 'submitted')" class="stat-card text-left cursor-pointer group"
            style="<?php echo e($stats['submitted'] > 0 ? 'border-color: #a7f3d0; background: rgba(236, 253, 245, 0.5);' : ''); ?>">
            <div class="flex items-center gap-3">
                <div class="stat-card-icon bg-emerald-100 text-emerald-600">
                    <iconify-icon icon="solar:check-circle-bold-duotone" width="20"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <div class="stat-card-value !mt-0 <?php echo e($stats['submitted'] > 0 ? '!text-emerald-700' : ''); ?>"><?php echo e($stats['submitted']); ?></div>
                    <span class="stat-card-label !mt-0"><?php echo e(__('pages.forms.stat_submitted')); ?></span>
                </div>
            </div>
        </button>
        
        <button type="button" wire:click="$set('tab', 'expired')" class="stat-card text-left cursor-pointer group">
            <div class="flex items-center gap-3">
                <div class="stat-card-icon bg-slate-200 text-slate-500">
                    <iconify-icon icon="solar:lock-keyhole-bold-duotone" width="20"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <div class="stat-card-value !mt-0 !text-slate-600"><?php echo e($stats['expired']); ?></div>
                    <span class="stat-card-label !mt-0"><?php echo e(__('pages.forms.stat_expired')); ?></span>
                </div>
            </div>
        </button>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="mb-8 sm:mb-10">
        <div class="tab-bar w-full">
            <?php
                $tabs = [
                    ['key' => 'pending',   'icon' => 'solar:clipboard-text-linear', 'label' => __('pages.forms.to_fill'),       'count' => ($loadStage >= 2) ? $stats['pending'] : null,  'countColor' => 'accent'],
                    ['key' => 'overdue',   'icon' => 'solar:alarm-linear',          'label' => __('pages.forms.stat_overdue'),   'count' => ($loadStage >= 2) ? $stats['overdue'] : null,  'countColor' => 'red'],
                    ['key' => 'submitted', 'icon' => 'solar:check-read-linear',     'label' => __('pages.forms.submitted'),      'count' => null, 'countColor' => null],
                    ['key' => 'expired',   'icon' => 'solar:lock-keyhole-linear',   'label' => __('pages.forms.stat_expired'),   'count' => null, 'countColor' => null],
                    ['key' => 'all',       'icon' => 'solar:list-linear',           'label' => __('pages.forms.all'),            'count' => null, 'countColor' => null],
                ];
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <button type="button" wire:click="$set('tab', '<?php echo e($t['key']); ?>')"
                        class="tab-bar-item flex items-center justify-center gap-1.5 sm:gap-2 touch-manipulation whitespace-nowrap <?php echo e($tab === $t['key'] ? 'tab-bar-item-active' : 'tab-bar-item-default'); ?>">
                    <iconify-icon icon="<?php echo e($t['icon']); ?>" width="15"></iconify-icon>
                    <span><?php echo e($t['label']); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t['count'] && $t['count'] > 0): ?>
                        <span class="ml-0.5 inline-flex items-center justify-center h-5 min-w-[20px] px-1.5 rounded-full text-[10px] font-bold
                            <?php echo e($t['countColor'] === 'red'
                                ? ($tab === $t['key'] ? 'bg-red-100 text-red-700' : 'bg-red-100 text-red-600')
                                : ($tab === $t['key'] ? 'bg-[var(--accent)]/15 text-[var(--accent)]' : 'bg-slate-200 text-slate-600')); ?>"><?php echo e($t['count']); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($loadStage >= 2): ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->teamForms->isNotEmpty() && in_array($tab, ['pending', 'all'])): ?>
        <section class="mb-8 sm:mb-10">
            
            <div class="flex items-center gap-2.5 mb-4 sm:mb-5">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-500">
                    <iconify-icon icon="solar:users-group-rounded-bold" width="16"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <h2 class="text-sm font-bold text-slate-900"><?php echo e(__('pages.forms.team_forms_title')); ?></h2>
                    <p class="text-xs text-slate-500 leading-snug"><?php echo e(__('pages.forms.team_forms_help')); ?></p>
                </div>
            </div>

            
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->teamForms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teamForm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <div class="content-card group hover:shadow-md transition-all duration-200">
                        <div class="p-4 sm:p-5 flex flex-col h-full">
                            
                            <div class="flex items-start gap-3 mb-auto min-w-0">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-500 group-hover:scale-105 transition-transform">
                                    <iconify-icon icon="solar:clipboard-text-bold-duotone" width="20"></iconify-icon>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-bold text-slate-900 truncate group-hover:text-[var(--accent)] transition-colors"><?php echo e($teamForm->name); ?></h3>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($teamForm->description): ?>
                                        <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed"><?php echo e($teamForm->description); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <span class="inline-flex items-center gap-1 mt-2.5 text-[10px] font-bold uppercase tracking-wider text-violet-600 bg-violet-50 rounded-full px-2 py-0.5">
                                        <iconify-icon icon="solar:users-group-rounded-linear" width="11"></iconify-icon>
                                        <?php echo e(__('pages.forms.team_form_badge')); ?>

                                    </span>
                                </div>
                            </div>

                            
                            <div class="mt-4 pt-3" style="border-top: 1px solid #f1f5f9;">
                                <a href="<?php echo e($teamForm->slug ? route('forms.fill-team-by-slug', $teamForm->slug) : route('forms.fill-team', $teamForm)); ?>"
                                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-white rounded-xl shadow-sm hover:opacity-90 hover:shadow-md transition-all touch-manipulation"
                                   style="background-color: var(--accent);">
                                    <iconify-icon icon="solar:pen-bold" width="14"></iconify-icon>
                                    <?php echo e(__('pages.forms.fill')); ?>

                                </a>
                            </div>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <section>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->teamForms->isNotEmpty() && in_array($tab, ['pending', 'all'])): ?>
            <div class="flex items-center gap-2.5 mb-4 sm:mb-5">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[var(--accent-soft)] text-[var(--accent)]">
                    <iconify-icon icon="solar:user-check-bold" width="16"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <h2 class="text-sm font-bold text-slate-900"><?php echo e(__('pages.forms.assigned_to_you')); ?></h2>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="space-y-3 sm:space-y-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <?php
                    $aStatus = $a->status instanceof \App\Enums\FormAssignmentStatus ? $a->status->value : (string) $a->status;
                    $isActionable = !in_array($aStatus, ['submitted', 'expired']);

                    $cardStyle = match($aStatus) {
                        'overdue'   => ['accent' => '#f87171', 'bg' => 'bg-red-50/40',     'icon_bg' => 'bg-red-50 text-red-600',                          'icon' => 'solar:alarm-bold-duotone'],
                        'submitted' => ['accent' => '#34d399', 'bg' => '',                  'icon_bg' => 'bg-emerald-50 text-emerald-600',                  'icon' => 'solar:check-circle-bold-duotone'],
                        'expired'   => ['accent' => '#cbd5e1', 'bg' => '',                  'icon_bg' => 'bg-slate-100 text-slate-400',                     'icon' => 'solar:lock-keyhole-bold-duotone'],
                        default     => ['accent' => 'var(--accent)', 'bg' => '',            'icon_bg' => 'bg-[var(--accent-soft)] text-[var(--accent)]',    'icon' => 'solar:clipboard-text-bold-duotone'],
                    };

                    $badgeStyle = match($aStatus) {
                        'submitted' => 'background: rgba(236,253,245,1); color: #047857; border-color: #a7f3d0;',
                        'overdue'   => 'background: rgba(254,242,242,1); color: #b91c1c; border-color: #fecaca;',
                        'expired'   => 'background: #f1f5f9; color: #64748b; border-color: #e2e8f0;',
                        default     => 'background: var(--accent-soft); color: var(--accent); border-color: color-mix(in srgb, var(--accent) 20%, transparent);',
                    };

                    $badgeIcon = match($aStatus) {
                        'submitted' => 'solar:check-circle-bold',
                        'overdue'   => 'solar:alarm-bold',
                        'expired'   => 'solar:lock-keyhole-bold',
                        default     => 'solar:clock-circle-bold',
                    };
                ?>

                <article class="content-card <?php echo e($cardStyle['bg']); ?> hover:shadow-md transition-all duration-200 group <?php echo e($aStatus === 'expired' ? 'opacity-70' : ''); ?>"
                         style="border-left: 3px solid <?php echo e($cardStyle['accent']); ?>;">
                    <div class="p-4 sm:p-5">
                        
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                            
                            <div class="flex items-start gap-3.5 min-w-0 flex-1">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl <?php echo e($cardStyle['icon_bg']); ?> group-hover:scale-105 transition-transform duration-200">
                                    <iconify-icon icon="<?php echo e($cardStyle['icon']); ?>" width="22"></iconify-icon>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isActionable): ?>
                                        <a href="<?php echo e(route('forms.fill', $a)); ?>" class="group/title block">
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <h3 class="text-sm sm:text-[15px] font-bold truncate <?php echo e($aStatus === 'expired' ? 'text-slate-500' : 'text-slate-900'); ?> <?php echo e($isActionable ? 'group-hover/title:text-[var(--accent)] transition-colors' : ''); ?>">
                                            <?php echo e($a->form?->name ?? '—'); ?>

                                        </h3>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isActionable): ?>
                                        </a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 mt-2">
                                        <span class="inline-flex items-center gap-1.5 text-xs text-slate-500">
                                            <iconify-icon icon="solar:user-linear" width="13" class="text-slate-400"></iconify-icon>
                                            <?php echo e(__('pages.forms.by')); ?>

                                            <span class="font-medium text-slate-700"><?php echo e($a->assignedBy?->name ?? '—'); ?></span>
                                        </span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($a->due_date): ?>
                                            <span class="inline-flex items-center gap-1.5 text-xs <?php echo e($aStatus === 'overdue' ? 'text-red-600 font-semibold' : 'text-slate-500'); ?>">
                                                <iconify-icon icon="solar:calendar-linear" width="13" class="<?php echo e($aStatus === 'overdue' ? 'text-red-500' : 'text-slate-400'); ?>"></iconify-icon>
                                                <?php echo e(__('pages.forms.due_date')); ?> : <?php echo e($a->due_date->format('d/m/Y')); ?>

                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($a->expires_at): ?>
                                            <span class="inline-flex items-center gap-1.5 text-xs text-slate-500">
                                                <iconify-icon icon="solar:lock-keyhole-linear" width="13" class="text-slate-400"></iconify-icon>
                                                <?php echo e(__('pages.forms.expires_at')); ?> : <?php echo e($a->expires_at->format('d/m/Y H:i')); ?>

                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="flex items-center gap-2.5 shrink-0 pl-[3.625rem] sm:pl-0">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold" style="<?php echo e($badgeStyle); ?> border-width: 1px; border-style: solid;">
                                    <iconify-icon icon="<?php echo e($badgeIcon); ?>" width="12"></iconify-icon>
                                    <?php echo e($statusLabels[$aStatus] ?? $aStatus); ?>

                                </span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isActionable): ?>
                                    <a href="<?php echo e(route('forms.fill', $a)); ?>"
                                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 sm:py-2 text-xs font-bold text-white rounded-xl shadow-sm hover:opacity-90 hover:shadow-md transition-all touch-manipulation min-h-[40px] sm:min-h-0"
                                       style="background-color: var(--accent);">
                                        <iconify-icon icon="solar:pen-bold" width="14"></iconify-icon>
                                        <?php echo e(__('pages.forms.fill')); ?>

                                    </a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </article>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                
                <div class="content-card">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'submitted'): ?>
                                <iconify-icon icon="solar:check-read-linear" width="28" class="text-slate-300"></iconify-icon>
                            <?php elseif($tab === 'overdue'): ?>
                                <iconify-icon icon="solar:alarm-linear" width="28" class="text-slate-300"></iconify-icon>
                            <?php elseif($tab === 'expired'): ?>
                                <iconify-icon icon="solar:lock-keyhole-linear" width="28" class="text-slate-300"></iconify-icon>
                            <?php else: ?>
                                <iconify-icon icon="solar:clipboard-check-linear" width="28" class="text-slate-300"></iconify-icon>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <p class="empty-state-title"><?php echo e(__('pages.forms.no_forms')); ?></p>
                        <p class="empty-state-text">
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
                            <button type="button" wire:click="$set('tab', 'all')"
                                    class="mt-5 inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors touch-manipulation" style="border: 1px solid #e2e8f0;">
                                <iconify-icon icon="solar:list-linear" width="16"></iconify-icon>
                                <?php echo e(__('pages.forms.see_all')); ?>

                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    <?php else: ?>
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-8 sm:mb-10">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 4; $i++): ?>
                <div class="stat-card animate-pulse">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-slate-100"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-6 w-12 bg-slate-100 rounded-lg"></div>
                            <div class="h-3 w-16 bg-slate-50 rounded"></div>
                        </div>
                    </div>
                </div>
            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="space-y-3 sm:space-y-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 4; $i++): ?>
                <div class="content-card animate-pulse" style="border-left: 3px solid #e2e8f0;">
                    <div class="p-4 sm:p-5 flex items-center gap-3.5">
                        <div class="h-11 w-11 rounded-xl bg-slate-100 shrink-0"></div>
                        <div class="flex-1 space-y-2.5">
                            <div class="h-4 bg-slate-100 rounded-lg w-3/5"></div>
                            <div class="h-3 bg-slate-50 rounded w-2/5"></div>
                        </div>
                        <div class="hidden sm:block h-7 w-20 bg-slate-100 rounded-full shrink-0"></div>
                        <div class="hidden sm:block h-9 w-24 bg-slate-100 rounded-xl shrink-0"></div>
                    </div>
                </div>
            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/livewire/user-forms/index.blade.php ENDPATH**/ ?>