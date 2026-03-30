
<?php
    use App\Enums\TicketStatus;
    use App\Models\OrganizationFunction;
    use App\Models\TicketPriority;
    use App\Models\User;

    /** @var iterable<int, TicketPriority> $sidebarOrgPriorities */
    $sidebarOrgPriorities = $orgPriorities ?? [];
    /** @var iterable<int, User> $sidebarStaffUsers */
    $sidebarStaffUsers = $staffUsers ?? [];
    /** @var iterable<int, OrganizationFunction> $sidebarOrgFunctions */
    $sidebarOrgFunctions = $organizationFunctions ?? [];

    $roleLabels = [
        'owner' => __('Admin'),
        'admin' => __('Admin'),
        'agent' => __('Agent'),
        'member' => __('Membre'),
    ];
    $priorityDotClass = [1 => 'bg-slate-400', 2 => 'bg-blue-500', 3 => 'bg-amber-500', 4 => 'bg-red-500'];
    $priorityLevel = optional($ticket->priority)->level ?? 2;
    $priorityDot = $priorityDotClass[$priorityLevel] ?? 'bg-slate-400';
    $statusLabels = [
        'open' => __('tickets.status.open'),
        'in_progress' => __('tickets.status.in_progress'),
        'pending' => __('tickets.status.pending'),
        'resolved' => __('tickets.status.resolved'),
        'closed' => __('tickets.status.closed'),
    ];
    $sidebarStatusOptions = collect(TicketStatus::cases())->map(fn ($s) => [
        'value' => $s->value,
        'label' => __('tickets.status.'.$s->value),
    ])->all();
    $sidebarPriorityOptions = collect($sidebarOrgPriorities)->map(fn ($p) => [
        'value' => $p->id,
        'label' => $p->name,
    ])->all();
    $sidebarGroupOptions = [['value' => '', 'label' => __('— Aucun groupe')]];
    foreach (($ticketGroups ?? collect()) as $tg) {
        $sidebarGroupOptions[] = ['value' => (string) $tg->id, 'label' => $tg->name];
    }
    $sidebarFunctionOptions = [['value' => '', 'label' => __('— Aucune —')]];
    foreach ($sidebarOrgFunctions as $fn) {
        $sidebarFunctionOptions[] = ['value' => (string) $fn->id, 'label' => $fn->name];
    }
?>
<div class="flex flex-col h-full gap-4 min-w-0">
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isLocked ?? false): ?>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-3 sm:p-4 shadow-sm min-w-0">
            <div class="flex items-start gap-2 text-sm text-amber-800">
                <iconify-icon icon="solar:lock-keyhole-bold-duotone" width="20" class="text-amber-600 shrink-0 mt-0.5"></iconify-icon>
                <div>
                    <div class="font-bold"><?php echo e(__('tickets.locked_banner')); ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canBypassLock ?? false): ?>
                        <div class="text-xs text-amber-700/80 mt-0.5"><?php echo e(__('Vous avez les droits administrateur pour modifier ce ticket.')); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Aperçu (aligné détail ticket) -->
    <div class="rounded-2xl border border-slate-200 bg-white p-3 sm:p-4 shadow-sm min-w-0">
        <div class="flex flex-col gap-3 min-w-0">
            <div class="flex items-start gap-2 min-w-0">
                <span class="shrink-0 inline-flex items-center justify-center rounded-lg px-2.5 py-1.5 min-w-[6rem] text-[11px] font-semibold font-mono tracking-tight bg-[var(--accent-soft)] text-[var(--accent)] border border-[var(--accent-soft)]">
                    <?php echo e($ticket->shortReference()); ?>

                </span>
                <span class="text-sm font-bold text-slate-900 line-clamp-2 min-w-0 leading-snug"><?php echo e($ticket->subject); ?></span>
            </div>
            <a href="<?php echo e(route('tickets.discussion', $ticket)); ?>" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors w-full sm:w-auto">
                <iconify-icon icon="solar:arrow-right-linear" width="14"></iconify-icon>
                <?php echo e(__('Ouvrir')); ?>

            </a>
        </div>

        
        <div class="mt-3 space-y-2.5">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 min-w-0">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                    <?php echo e(__('Statut')); ?>

                    <span wire:loading wire:target="changeStatus" class="inline-block h-3 w-3 rounded-full border-2 border-slate-300 border-t-transparent animate-spin align-middle ml-1"></span>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAssignTicket ?? false): ?>
                    <?php if (isset($component)) { $__componentOriginal67b35608722bcee218637ec24a02b934 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67b35608722bcee218637ec24a02b934 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-select','data' => ['options' => $sidebarStatusOptions,'label' => $statusLabels[$ticket->status->value] ?? $ticket->status->value,'selectedValue' => $ticket->status->value,'wireMethod' => 'changeStatus','instanceKey' => 'sidebar-status','disabled' => ($isLocked ?? false) && !($canBypassLock ?? false),'wire:loading.class' => 'opacity-50','wire:target' => 'changeStatus']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarStatusOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statusLabels[$ticket->status->value] ?? $ticket->status->value),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ticket->status->value),'wire-method' => 'changeStatus','instance-key' => 'sidebar-status','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($isLocked ?? false) && !($canBypassLock ?? false)),'wire:loading.class' => 'opacity-50','wire:target' => 'changeStatus']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal67b35608722bcee218637ec24a02b934)): ?>
<?php $attributes = $__attributesOriginal67b35608722bcee218637ec24a02b934; ?>
<?php unset($__attributesOriginal67b35608722bcee218637ec24a02b934); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal67b35608722bcee218637ec24a02b934)): ?>
<?php $component = $__componentOriginal67b35608722bcee218637ec24a02b934; ?>
<?php unset($__componentOriginal67b35608722bcee218637ec24a02b934); ?>
<?php endif; ?>
                <?php else: ?>
                    <div class="inline-flex max-w-full items-center gap-1.5 rounded-lg bg-[var(--accent-soft)] px-3 py-2.5 text-sm font-bold text-[var(--accent)]">
                        <iconify-icon icon="solar:bolt-circle-bold-duotone" width="14"></iconify-icon>
                        <span class="truncate"><?php echo e(__('tickets.status.' . $ticket->status->value)); ?></span>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 min-w-0">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                    <?php echo e(__('Priorité')); ?>

                    <span wire:loading wire:target="changePriority" class="inline-block h-3 w-3 rounded-full border-2 border-slate-300 border-t-transparent animate-spin align-middle ml-1"></span>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAssignTicket ?? false): ?>
                    <?php if (isset($component)) { $__componentOriginal67b35608722bcee218637ec24a02b934 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67b35608722bcee218637ec24a02b934 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-select','data' => ['options' => $sidebarPriorityOptions,'label' => optional($ticket->priority)->name ?? '—','selectedValue' => $ticket->ticket_priority_id,'wireMethod' => 'changePriority','instanceKey' => 'sidebar-priority','disabled' => ($isLocked ?? false) && !($canBypassLock ?? false),'wire:loading.class' => 'opacity-50','wire:target' => 'changePriority']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarPriorityOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(optional($ticket->priority)->name ?? '—'),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ticket->ticket_priority_id),'wire-method' => 'changePriority','instance-key' => 'sidebar-priority','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($isLocked ?? false) && !($canBypassLock ?? false)),'wire:loading.class' => 'opacity-50','wire:target' => 'changePriority']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal67b35608722bcee218637ec24a02b934)): ?>
<?php $attributes = $__attributesOriginal67b35608722bcee218637ec24a02b934; ?>
<?php unset($__attributesOriginal67b35608722bcee218637ec24a02b934); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal67b35608722bcee218637ec24a02b934)): ?>
<?php $component = $__componentOriginal67b35608722bcee218637ec24a02b934; ?>
<?php unset($__componentOriginal67b35608722bcee218637ec24a02b934); ?>
<?php endif; ?>
                <?php else: ?>
                    <div class="inline-flex max-w-full items-center gap-1.5 rounded-lg bg-white px-2.5 py-2 text-xs font-bold text-slate-700 border border-slate-200">
                        <span class="h-1.5 w-1.5 rounded-full <?php echo e($priorityDot); ?>"></span>
                        <span class="truncate"><?php echo e(optional($ticket->priority)->name ?? '—'); ?></span>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 min-w-0">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5"><?php echo e(__('Catégorie')); ?></div>
                <div class="flex min-w-0 items-center gap-2 rounded-lg bg-white px-3 py-2.5 text-sm font-medium text-slate-700 border border-slate-200">
                    <iconify-icon icon="solar:tag-bold-duotone" width="14"></iconify-icon>
                    <span class="truncate"><?php echo e(optional($ticket->category)->name ?? '—'); ?></span>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($ticketGroups ?? collect())->isNotEmpty()): ?>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 min-w-0">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                        <?php echo e(__('Groupe')); ?>

                        <span wire:loading wire:target="changeGroup" class="inline-block h-3 w-3 rounded-full border-2 border-slate-300 border-t-transparent animate-spin align-middle ml-1"></span>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAssignTicket ?? false): ?>
                        <?php if (isset($component)) { $__componentOriginal67b35608722bcee218637ec24a02b934 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67b35608722bcee218637ec24a02b934 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-select','data' => ['options' => $sidebarGroupOptions,'label' => $ticket->group?->name ?? __('— Aucun groupe'),'selectedValue' => $ticket->ticket_group_id !== null ? (string) $ticket->ticket_group_id : '','wireMethod' => 'changeGroup','instanceKey' => 'sidebar-group','disabled' => ($isLocked ?? false) && !($canBypassLock ?? false),'wire:loading.class' => 'opacity-50','wire:target' => 'changeGroup']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarGroupOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ticket->group?->name ?? __('— Aucun groupe')),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ticket->ticket_group_id !== null ? (string) $ticket->ticket_group_id : ''),'wire-method' => 'changeGroup','instance-key' => 'sidebar-group','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($isLocked ?? false) && !($canBypassLock ?? false)),'wire:loading.class' => 'opacity-50','wire:target' => 'changeGroup']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal67b35608722bcee218637ec24a02b934)): ?>
<?php $attributes = $__attributesOriginal67b35608722bcee218637ec24a02b934; ?>
<?php unset($__attributesOriginal67b35608722bcee218637ec24a02b934); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal67b35608722bcee218637ec24a02b934)): ?>
<?php $component = $__componentOriginal67b35608722bcee218637ec24a02b934; ?>
<?php unset($__componentOriginal67b35608722bcee218637ec24a02b934); ?>
<?php endif; ?>
                    <?php else: ?>
                        <div class="inline-flex max-w-full items-center gap-1.5 rounded-lg px-2.5 py-2 text-xs font-bold border" style="background-color: <?php echo e($ticket->group?->color ?? '#f8fafc'); ?>15; color: <?php echo e($ticket->group?->color ?? '#334155'); ?>; border-color: <?php echo e($ticket->group?->color ?? '#e2e8f0'); ?>33;">
                            <iconify-icon icon="solar:widget-5-bold-duotone" width="14"></iconify-icon>
                            <span class="min-w-0 break-words leading-snug"><?php echo e($ticket->group?->name ?? __('— Aucun groupe')); ?></span>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="mt-4 space-y-3 text-sm">
            
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 min-w-0">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-0.5"><?php echo e(__('Assignés')); ?></div>
                <div class="mt-1 flex flex-col gap-2 min-w-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->assignees->isNotEmpty()): ?>
                        <?php
                            $responsible = $ticket->assignees->first(fn ($u) => ($u->pivot->role ?? '') === 'responsible');
                            $collaborators = $ticket->assignees->filter(fn ($u) => ($u->pivot->role ?? '') !== 'responsible');
                        ?>
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($responsible): ?>
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="h-6 w-6 rounded-full flex items-center justify-center text-[10px] font-semibold shrink-0 ring-2 ring-[var(--accent)]" style="background: var(--accent-soft); color: var(--accent);">
                                    <?php echo e(strtoupper(mb_substr($responsible->name ?? '?', 0, 1))); ?>

                                </div>
                                <span class="text-sm font-semibold text-slate-900 truncate flex-1 min-w-0"><?php echo e($responsible->name); ?></span>
                                <span class="text-[10px] font-bold text-white bg-[var(--accent)] px-1.5 py-0.5 rounded-full shrink-0"><?php echo e(__('Responsable')); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($canAssignTicket ?? false) && !($isLocked ?? false)): ?>
                                    <button type="button" @click="$dispatch('confirm-action', { title: '<?php echo e(__('Retirer')); ?>', message: '<?php echo e(__('Retirer cet assigné du ticket ?')); ?>', confirmLabel: '<?php echo e(__('Retirer')); ?>', variant: 'danger', onConfirm: () => $wire.removeAssignee(<?php echo e($responsible->id); ?>) })" class="shrink-0 text-slate-400 hover:text-red-500 transition-colors" title="<?php echo e(__('Retirer')); ?>">
                                        <iconify-icon icon="solar:close-circle-linear" width="16"></iconify-icon>
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $collaborators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="h-6 w-6 rounded-full flex items-center justify-center text-[10px] font-semibold shrink-0 ring-1 ring-slate-200" style="background: var(--accent-soft); color: var(--accent);">
                                    <?php echo e(strtoupper(mb_substr($asg->name ?? '?', 0, 1))); ?>

                                </div>
                                <span class="text-sm font-semibold text-slate-900 truncate flex-1 min-w-0"><?php echo e($asg->name); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($canAssignTicket ?? false) && !($isLocked ?? false)): ?>
                                    <button type="button" wire:click="promoteToResponsible(<?php echo e($asg->id); ?>)" class="shrink-0 text-[10px] font-bold text-[var(--accent)] hover:underline" title="<?php echo e(__('Promouvoir en responsable')); ?>">
                                        <iconify-icon icon="solar:star-bold" width="14"></iconify-icon>
                                    </button>
                                    <button type="button" @click="$dispatch('confirm-action', { title: '<?php echo e(__('Retirer')); ?>', message: '<?php echo e(__('Retirer cet assigné du ticket ?')); ?>', confirmLabel: '<?php echo e(__('Retirer')); ?>', variant: 'danger', onConfirm: () => $wire.removeAssignee(<?php echo e($asg->id); ?>) })" class="shrink-0 text-slate-400 hover:text-red-500 transition-colors" title="<?php echo e(__('Retirer')); ?>">
                                        <iconify-icon icon="solar:close-circle-linear" width="16"></iconify-icon>
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php else: ?>
                        <span class="text-sm text-slate-400 italic">—</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($groupMembers ?? collect())->isNotEmpty()): ?>
                        <div class="mt-2 pt-2 border-t border-slate-200/60">
                            <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">
                                <?php echo e(__('Équipe')); ?> · <?php echo e($ticket->group?->name); ?>

                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $groupMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <?php
                                    $isAlreadyAssigned = $ticket->assignees->contains('id', $gm->id);
                                ?>
                                <div class="flex items-center gap-2 min-w-0 py-1 <?php echo e($isAlreadyAssigned ? 'opacity-40' : ''); ?>">
                                    <div class="h-6 w-6 rounded-full flex items-center justify-center text-[10px] font-semibold shrink-0" style="background: <?php echo e($ticket->group?->color ?? 'var(--accent)'); ?>20; color: <?php echo e($ticket->group?->color ?? 'var(--accent)'); ?>;">
                                        <?php echo e(strtoupper(mb_substr($gm->name, 0, 1))); ?>

                                    </div>
                                    <span class="text-sm text-slate-700 truncate flex-1 min-w-0"><?php echo e($gm->name); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAlreadyAssigned): ?>
                                        <span class="text-[9px] text-slate-400 shrink-0"><?php echo e(__('assigné')); ?></span>
                                    <?php elseif(($canAssignTicket ?? false) && !($isLocked ?? false)): ?>
                                        <button type="button" wire:click="addAssignee(<?php echo e($gm->id); ?>)" class="shrink-0 text-[10px] font-semibold text-[var(--accent)] hover:underline">
                                            <?php echo e(__('Assigner')); ?>

                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($canAssignTicket ?? false) && !($isLocked ?? false)): ?>
                        <div class="space-y-2.5 pt-1">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->assignees->isEmpty() || (auth()->id() && !$ticket->assignees->contains('id', auth()->id()))): ?>
                                <button type="button" wire:click="assignToMe" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-[var(--accent)] px-3 py-2.5 text-sm font-bold text-white shadow-sm hover:opacity-90 transition-all">
                                    <iconify-icon icon="solar:user-check-bold" width="16"></iconify-icon>
                                    <?php echo e(__("M'assigner")); ?>

                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="space-y-2" x-data="{ selectedAssignee: 0 }">
                                <label for="sidebar-add-assignee" class="sr-only"><?php echo e(__('Assigner à…')); ?></label>
                                <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['id' => 'sidebar-add-assignee','name' => 'sidebar_add_assignee','xModel.number' => 'selectedAssignee']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'sidebar-add-assignee','name' => 'sidebar_add_assignee','x-model.number' => 'selectedAssignee']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                    <option value="0"><?php echo e(__('Assigner à…')); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sidebarStaffUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36)): ?>
<?php $attributes = $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36; ?>
<?php unset($__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36)): ?>
<?php $component = $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36; ?>
<?php unset($__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36); ?>
<?php endif; ?>
                                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" :disabled="selectedAssignee <= 0" @click="$wire.addAssignee(Number(selectedAssignee)); selectedAssignee = 0;">
                                    <?php echo e(__('Ajouter')); ?>

                                </button>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 min-w-0">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5"><?php echo e(__('Fonction')); ?></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canSeeInternalNotes ?? false): ?>
                    <?php if (isset($component)) { $__componentOriginal67b35608722bcee218637ec24a02b934 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67b35608722bcee218637ec24a02b934 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-select','data' => ['options' => $sidebarFunctionOptions,'label' => $ticket->assignedToFunction?->name ?? __('— Aucune —'),'selectedValue' => $ticket->assigned_to_function_id !== null ? (string) $ticket->assigned_to_function_id : '','wireMethod' => 'setAssignedToFunction','instanceKey' => 'sidebar-function','disabled' => ($isLocked ?? false) && !($canBypassLock ?? false)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarFunctionOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ticket->assignedToFunction?->name ?? __('— Aucune —')),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ticket->assigned_to_function_id !== null ? (string) $ticket->assigned_to_function_id : ''),'wire-method' => 'setAssignedToFunction','instance-key' => 'sidebar-function','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($isLocked ?? false) && !($canBypassLock ?? false))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal67b35608722bcee218637ec24a02b934)): ?>
<?php $attributes = $__attributesOriginal67b35608722bcee218637ec24a02b934; ?>
<?php unset($__attributesOriginal67b35608722bcee218637ec24a02b934); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal67b35608722bcee218637ec24a02b934)): ?>
<?php $component = $__componentOriginal67b35608722bcee218637ec24a02b934; ?>
<?php unset($__componentOriginal67b35608722bcee218637ec24a02b934); ?>
<?php endif; ?>
                <?php else: ?>
                    <div class="mt-1 min-w-0">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->assignedToFunction ?? null): ?>
                            <span class="text-sm font-semibold text-slate-900 truncate block"><?php echo e($ticket->assignedToFunction->name); ?></span>
                        <?php else: ?>
                            <span class="text-sm text-slate-400 italic">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($functionMembers ?? collect())->isNotEmpty()): ?>
                    <div class="mt-2 pt-2 border-t border-slate-200/60">
                        <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">
                            <?php echo e(__('Membres')); ?> · <?php echo e($ticket->assignedToFunction?->name); ?>

                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $functionMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <?php $isAlreadyAssigned = $ticket->assignees->contains('id', $fm->id); ?>
                            <div class="flex items-center gap-2 min-w-0 py-1 <?php echo e($isAlreadyAssigned ? 'opacity-40' : ''); ?>">
                                <div class="h-6 w-6 rounded-full flex items-center justify-center text-[10px] font-semibold shrink-0 bg-[var(--accent-soft)] text-[var(--accent)]">
                                    <?php echo e(strtoupper(mb_substr($fm->name, 0, 1))); ?>

                                </div>
                                <span class="text-sm text-slate-700 truncate flex-1 min-w-0"><?php echo e($fm->name); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAlreadyAssigned): ?>
                                    <span class="text-[9px] text-slate-400 shrink-0"><?php echo e(__('assigné')); ?></span>
                                <?php elseif(($canAssignTicket ?? false) && !($isLocked ?? false)): ?>
                                    <button type="button" wire:click="addAssignee(<?php echo e($fm->id); ?>)" class="shrink-0 text-[10px] font-semibold text-[var(--accent)] hover:underline">
                                        <?php echo e(__('Assigner')); ?>

                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 min-w-0">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5"><?php echo e(__('Échéance')); ?></div>
                <div class="mt-1 flex items-center gap-2 min-w-0">
                    <iconify-icon icon="solar:calendar-add-linear" width="16" class="text-slate-400 shrink-0"></iconify-icon>
                    <?php
                        /** @var \Carbon\Carbon|null $ticketDueDate */
                        $ticketDueDate = $ticket->due_date;
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditDueDate ?? false): ?>
                        <input
                            id="ticket_due_date"
                            name="ticket_due_date"
                            type="date"
                            value="<?php echo e($ticketDueDate?->format('Y-m-d') ?? ''); ?>"
                            wire:change="updateDueDate($event.target.value)"
                            class="min-w-0 w-full rounded-lg border-slate-200 bg-white py-2 pl-3 pr-2 text-sm font-semibold text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] max-w-full disabled:opacity-50 disabled:cursor-not-allowed"
                            <?php if(($isLocked ?? false) && !($canBypassLock ?? false)): echo 'disabled'; endif; ?>
                        />
                    <?php else: ?>
                        <span class="text-sm font-semibold text-slate-900 truncate">
                            <?php echo e($ticketDueDate ? $ticketDueDate->translatedFormat('d M Y') : '—'); ?>

                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->sla_policy_id): ?>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 min-w-0"
                     x-data="{
                         frSecs: <?php echo e($ticket->slaFirstResponseRemainingSeconds() ?? 'null'); ?>,
                         resSecs: <?php echo e($ticket->slaResolutionRemainingSeconds() ?? 'null'); ?>,
                         interval: null,
                         init() {
                             this.interval = setInterval(() => {
                                 if (this.frSecs !== null && this.frSecs > 0) this.frSecs--;
                                 if (this.resSecs !== null && this.resSecs > 0) this.resSecs--;
                             }, 1000);
                         },
                         destroy() { clearInterval(this.interval); },
                         format(s) {
                             if (s === null) return '—';
                             if (s <= 0) return 'Expiré';
                             let h = Math.floor(s / 3600);
                             let m = Math.floor((s % 3600) / 60);
                             return h > 0 ? h + 'h ' + m + 'min' : m + 'min';
                         }
                     }">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500"><?php echo e(__('SLA')); ?></div>
                    <div class="mt-2 space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs text-slate-600"><?php echo e(__('Première réponse')); ?></span>
                            <div class="flex items-center gap-1.5">
                                <?php if (isset($component)) { $__componentOriginalf8537846ecb099bfba89af16a4fcaefa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8537846ecb099bfba89af16a4fcaefa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sla-badge','data' => ['status' => $ticket->slaFirstResponseStatus(),'type' => 'fr']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sla-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ticket->slaFirstResponseStatus()),'type' => 'fr']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8537846ecb099bfba89af16a4fcaefa)): ?>
<?php $attributes = $__attributesOriginalf8537846ecb099bfba89af16a4fcaefa; ?>
<?php unset($__attributesOriginalf8537846ecb099bfba89af16a4fcaefa); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8537846ecb099bfba89af16a4fcaefa)): ?>
<?php $component = $__componentOriginalf8537846ecb099bfba89af16a4fcaefa; ?>
<?php unset($__componentOriginalf8537846ecb099bfba89af16a4fcaefa); ?>
<?php endif; ?>
                                <span class="text-[11px] font-mono text-slate-500" x-text="format(frSecs)"></span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs text-slate-600"><?php echo e(__('Résolution')); ?></span>
                            <div class="flex items-center gap-1.5">
                                <?php if (isset($component)) { $__componentOriginalf8537846ecb099bfba89af16a4fcaefa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8537846ecb099bfba89af16a4fcaefa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sla-badge','data' => ['status' => $ticket->slaResolutionStatus(),'type' => 'res']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sla-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ticket->slaResolutionStatus()),'type' => 'res']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8537846ecb099bfba89af16a4fcaefa)): ?>
<?php $attributes = $__attributesOriginalf8537846ecb099bfba89af16a4fcaefa; ?>
<?php unset($__attributesOriginalf8537846ecb099bfba89af16a4fcaefa); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8537846ecb099bfba89af16a4fcaefa)): ?>
<?php $component = $__componentOriginalf8537846ecb099bfba89af16a4fcaefa; ?>
<?php unset($__componentOriginalf8537846ecb099bfba89af16a4fcaefa); ?>
<?php endif; ?>
                                <span class="text-[11px] font-mono text-slate-500" x-text="format(resSecs)"></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->requires_approval): ?>
        <div class="rounded-2xl border border-slate-200 bg-white p-3 sm:p-4 shadow-sm min-w-0">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-3"><?php echo e(__('Approbation')); ?></div>

            
            <div class="mb-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->approval_status === 'pending'): ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        <iconify-icon icon="solar:hourglass-bold-duotone" width="14"></iconify-icon>
                        En attente de validation
                    </span>
                <?php elseif($ticket->approval_status === 'approved'): ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <iconify-icon icon="solar:check-circle-bold-duotone" width="14"></iconify-icon>
                        Approuvé
                    </span>
                <?php elseif($ticket->approval_status === 'rejected'): ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                        <iconify-icon icon="solar:close-circle-bold-duotone" width="14"></iconify-icon>
                        Rejeté
                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->approvals->isNotEmpty()): ?>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $ticket->approvals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="rounded-xl border border-slate-100 bg-slate-50 p-2.5 text-xs">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($approval->status === 'pending'): ?>
                                <div class="flex items-center gap-1.5 text-amber-700">
                                    <iconify-icon icon="solar:hourglass-linear" width="12"></iconify-icon>
                                    <span>Demandé par <?php echo e($approval->requester?->name ?? 'Utilisateur'); ?></span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-1"><?php echo e($approval->created_at->diffForHumans()); ?></div>
                            <?php else: ?>
                                <div class="flex items-center gap-1.5 <?php echo e($approval->status === 'approved' ? 'text-emerald-700' : 'text-red-700'); ?>">
                                    <iconify-icon icon="<?php echo e($approval->status === 'approved' ? 'solar:check-circle-linear' : 'solar:close-circle-linear'); ?>" width="12"></iconify-icon>
                                    <span><?php echo e($approval->status === 'approved' ? 'Approuvé' : 'Rejeté'); ?> par <?php echo e($approval->approver?->name ?? '—'); ?></span>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($approval->comment): ?>
                                    <div class="mt-1.5 rounded-lg bg-white border border-slate-200 p-2 text-slate-700 italic">
                                        "<?php echo e($approval->comment); ?>"
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="text-[10px] text-slate-400 mt-1"><?php echo e($approval->decided_at?->diffForHumans() ?? $approval->updated_at->diffForHumans()); ?></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canApproveTicket ?? false): ?>
                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-100">
                    <button
                        wire:click="openApprovalModal('approve')"
                        class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-600 px-3 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition-colors"
                    >
                        <iconify-icon icon="solar:check-circle-linear" width="14"></iconify-icon>
                        Approuver
                    </button>
                    <button
                        wire:click="openApprovalModal('reject')"
                        class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl bg-red-600 px-3 py-2 text-xs font-bold text-white shadow-sm hover:bg-red-700 transition-colors"
                    >
                        <iconify-icon icon="solar:close-circle-linear" width="14"></iconify-icon>
                        Rejeter
                    </button>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($formResponse && !empty($formResponse->field_snapshot) && !empty($formResponse->responses)): ?>
        <div class="rounded-2xl border border-slate-200 bg-white p-3 sm:p-4 shadow-sm min-w-0">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-3"><?php echo e(__('Champs personnalisés')); ?></div>
            <div class="space-y-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $formResponse->field_snapshot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <?php
                        $fieldKey = $field['key'] ?? $field['id'] ?? null;
                        $fieldValue = $fieldKey ? ($formResponse->responses[$fieldKey] ?? null) : null;
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fieldValue !== null && $fieldValue !== '' && $fieldValue !== []): ?>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500"><?php echo e($field['label'] ?? $fieldKey); ?></div>
                            <div class="mt-1 text-sm text-slate-900 break-words min-w-0">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_array($fieldValue)): ?>
                                    <?php echo e(implode(', ', $fieldValue)); ?>

                                <?php elseif(($field['type'] ?? '') === 'boolean'): ?>
                                    <?php echo e($fieldValue ? __('Oui') : __('Non')); ?>

                                <?php else: ?>
                                    <?php echo e($fieldValue); ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Checklist -->
    <?php
        $checklistItems = $ticket->checklistItems ?? collect();
        $checklistTotal = $checklistItems->count();
        $checklistDone = $checklistItems->where('is_done', true)->count();
        $checklistPct = $checklistTotal > 0 ? (int) round(100 * $checklistDone / $checklistTotal) : 0;
    ?>
    <div class="rounded-2xl border border-slate-200 bg-white p-3 sm:p-4 shadow-sm min-w-0">
        <div class="flex items-center justify-between gap-2 min-w-0">
            <div class="min-w-0">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500"><?php echo e(__('Checklist')); ?></div>
                <div class="mt-1 text-sm font-semibold text-slate-900"><?php echo e($checklistDone); ?>/<?php echo e($checklistTotal); ?> (<?php echo e($checklistPct); ?>%)</div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($checklistTotal > 0): ?>
                <div class="flex-1 max-w-[120px] h-2 rounded-full bg-slate-200 overflow-hidden">
                    <div class="h-full rounded-full bg-[var(--accent)] transition-all duration-300" style="width: <?php echo e($checklistPct); ?>%"></div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($checklistTotal > 0): ?>
            <ul class="mt-4 space-y-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $checklistItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <?php
                        $canToggleThis = (($isStaffOrTicketOwner ?? false) || ($item->assigned_to && (int)$item->assigned_to === ($authUserId ?? 0)) || ($item->relationLoaded('assignees') && $item->assignees->contains('id', $authUserId ?? 0))) && !(($isLocked ?? false) && !($canBypassLock ?? false));
                    ?>
                    <li class="flex items-start gap-2 rounded-xl border border-slate-200 bg-slate-50 p-3" x-data="{ editing: false, editTitle: '<?php echo e(str_replace("'", "\\'", $item->title)); ?>' }">
                        <?php if($canToggleThis): ?>
                            <button type="button" wire:click="toggleChecklistItem(<?php echo e($item->id); ?>)" class="cursor-pointer mt-0.5 shrink-0 flex items-center justify-center h-5 w-5 rounded border-2 transition-colors <?php echo e($item->is_done ? 'bg-[var(--accent)] border-[var(--accent)] text-white' : 'border-slate-300 bg-white text-transparent hover:border-[var(--accent)]'); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->is_done): ?>
                                    <iconify-icon icon="solar:check-read-linear" width="12"></iconify-icon>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </button>
                        <?php else: ?>
                            <span class="mt-0.5 shrink-0 flex items-center justify-center h-5 w-5 rounded border-2 <?php echo e($item->is_done ? 'bg-[var(--accent)] border-[var(--accent)] text-white' : 'border-slate-300 bg-white'); ?>" title="<?php echo e(__('checklist_items.cannot_toggle')); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->is_done): ?>
                                    <iconify-icon icon="solar:check-read-linear" width="12"></iconify-icon>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="min-w-0 flex-1">
                            
                            <template x-if="!editing">
                                <span
                                    class="text-sm font-medium <?php echo e($item->is_done ? 'text-slate-500 line-through' : 'text-slate-900'); ?> <?php echo e(($canEditChecklist ?? false) ? 'cursor-pointer hover:text-[var(--accent)]' : ''); ?>"
                                    <?php if($canEditChecklist ?? false): ?> @click="editing = true; $nextTick(() => $refs['editInput<?php echo e($item->id); ?>']?.focus())" <?php endif; ?>
                                ><?php echo e($item->title); ?></span>
                            </template>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEditChecklist ?? false): ?>
                                <template x-if="editing">
                                    <input
                                        type="text"
                                        x-ref="editInput<?php echo e($item->id); ?>"
                                        x-model="editTitle"
                                        @blur="editing = false; if (editTitle.trim() && editTitle !== '<?php echo e(str_replace("'", "\\'", $item->title)); ?>') $wire.updateChecklistItemTitle(<?php echo e($item->id); ?>, editTitle)"
                                        @keydown.enter.prevent="$event.target.blur()"
                                        @keydown.escape.prevent="editing = false; editTitle = '<?php echo e(str_replace("'", "\\'", $item->title)); ?>'"
                                        class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                    />
                                </template>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->relationLoaded('assignees') && $item->assignees->isNotEmpty()): ?>
                                <div class="mt-0.5 flex flex-wrap items-center gap-1">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $item->assignees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemAsg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <span class="inline-flex items-center gap-1 text-[11px] <?php echo e(($itemAsg->pivot->role ?? '') === 'responsible' ? 'text-[var(--accent)] font-bold' : 'text-slate-500'); ?>">
                                            <span class="h-4 w-4 rounded-full flex items-center justify-center text-[8px] font-semibold shrink-0 <?php echo e(($itemAsg->pivot->role ?? '') === 'responsible' ? 'ring-1 ring-[var(--accent)]' : 'ring-1 ring-slate-200'); ?>" style="background: var(--accent-soft); color: var(--accent);"><?php echo e(strtoupper(mb_substr($itemAsg->name ?? '?', 0, 1))); ?></span>
                                            <?php echo e($itemAsg->name); ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($canEditChecklist ?? false) && !($isLocked ?? false)): ?>
                                                <button type="button" @click="$dispatch('confirm-action', { title: '<?php echo e(__('Retirer')); ?>', message: '<?php echo e(__('Retirer cet assigné de la tâche ?')); ?>', confirmLabel: '<?php echo e(__('Retirer')); ?>', variant: 'danger', onConfirm: () => $wire.removeChecklistItemAssignee(<?php echo e($item->id); ?>, <?php echo e($itemAsg->id); ?>) })" class="text-slate-400 hover:text-red-500" title="<?php echo e(__('Retirer')); ?>">
                                                    <iconify-icon icon="solar:close-circle-linear" width="12"></iconify-icon>
                                                </button>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            <?php elseif($item->assignee): ?>
                                <div class="mt-0.5 text-[11px] text-slate-500"><?php echo e(__('Responsable')); ?>: <?php echo e($item->assignee->name); ?></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($canEditChecklist ?? false) && !($isLocked ?? false)): ?>
                                <div class="mt-1" x-data="{ open: false }">
                                    <button type="button" @click="open = !open" class="text-[10px] font-medium text-[var(--accent)] hover:underline">
                                        + <?php echo e(__('Assigner')); ?>

                                    </button>
                                    <div x-show="open" x-cloak class="mt-1">
                                        <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['xOn:change' => 'if ($event.target.value > 0) { $wire.addChecklistItemAssignee('.e($item->id).', Number($event.target.value)); $event.target.selectedIndex = 0; open = false; }']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-on:change' => 'if ($event.target.value > 0) { $wire.addChecklistItemAssignee('.e($item->id).', Number($event.target.value)); $event.target.selectedIndex = 0; open = false; }']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                            <option value="0"><?php echo e(__('Assigner à…')); ?></option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sidebarStaffUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                <option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36)): ?>
<?php $attributes = $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36; ?>
<?php unset($__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36)): ?>
<?php $component = $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36; ?>
<?php unset($__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36); ?>
<?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->assigned_to_function_id && !$item->assigned_to): ?>
                                <div class="mt-0.5 flex items-center gap-2">
                                    <span class="text-[11px] text-amber-600 font-medium">
                                        <?php echo e($item->assignedToFunction?->name ?? '—'); ?> — <?php echo e(__('checklist_items.to_claim')); ?>

                                    </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array((int)$item->assigned_to_function_id, $userFunctionIds ?? [])): ?>
                                        <button type="button" wire:click="claimChecklistItem(<?php echo e($item->id); ?>)" class="text-[11px] font-bold text-[var(--accent)] hover:underline">
                                            <?php echo e(__('checklist_items.claim')); ?>

                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->due_date): ?>
                                <div class="mt-0.5 text-[11px] text-slate-500"><?php echo e(__('Échéance')); ?>: <?php echo e($item->due_date->translatedFormat('d M Y')); ?></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->is_done && $item->done_at): ?>
                                <div class="mt-1 text-[10px] text-slate-400">
                                    <?php echo e(__('checklist_items.done_by_at', ['name' => $item->doneByUser?->name ?? '—', 'time' => $item->done_at->format('H:i')])); ?>

                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <?php if(($canEditChecklist ?? false) && !(($isLocked ?? false) && !($canBypassLock ?? false))): ?>
                            <button type="button" @click="$dispatch('confirm-action', { title: '<?php echo e(__('Supprimer')); ?>', message: '<?php echo e(__('Supprimer cet élément ?')); ?>', confirmLabel: '<?php echo e(__('Supprimer')); ?>', variant: 'danger', onConfirm: () => $wire.deleteChecklistItem(<?php echo e($item->id); ?>) })" class="shrink-0 mt-0.5 text-slate-400 hover:text-red-500 transition-colors" title="<?php echo e(__('Supprimer')); ?>">
                                <iconify-icon icon="solar:trash-bin-trash-linear" width="14"></iconify-icon>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
        <?php else: ?>
            <p class="mt-3 text-sm text-slate-400"><?php echo e(__('Aucune étape.')); ?></p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(($canEditChecklist ?? false) && !(($isLocked ?? false) && !($canBypassLock ?? false))): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showAddChecklistItem ?? false): ?>
                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-3 space-y-2" x-data="{ assignMode: 'user' }">
                    <input type="text" wire:model="newChecklistTitle" class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm <?php $__errorArgs = ['newChecklistTitle'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e(__('Intitulé')); ?>" />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newChecklistTitle'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-xs text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    
                    <div class="flex items-center gap-2 text-xs">
                        <button type="button" @click="assignMode = 'user'; $wire.set('newChecklistAssignedToFunction', null)" :class="assignMode === 'user' ? 'bg-[var(--accent)] text-white' : 'bg-white text-slate-700 border border-slate-200'" class="rounded-lg px-2.5 py-1.5 font-medium transition-colors">
                            <?php echo e(__('Responsable')); ?>

                        </button>
                        <button type="button" @click="assignMode = 'function'; $wire.set('newChecklistAssignedTo', null)" :class="assignMode === 'function' ? 'bg-[var(--accent)] text-white' : 'bg-white text-slate-700 border border-slate-200'" class="rounded-lg px-2.5 py-1.5 font-medium transition-colors">
                            <?php echo e(__('checklist_items.assigned_to_function')); ?>

                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <template x-if="assignMode === 'user'">
                            <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['wire:model' => 'newChecklistAssignedTo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'newChecklistAssignedTo']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <option value=""><?php echo e(__('— Responsable')); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sidebarStaffUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <option value="<?php echo e((int) $u->id); ?>"><?php echo e($u->name); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36)): ?>
<?php $attributes = $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36; ?>
<?php unset($__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36)): ?>
<?php $component = $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36; ?>
<?php unset($__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36); ?>
<?php endif; ?>
                        </template>
                        <template x-if="assignMode === 'function'">
                            <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['wire:model' => 'newChecklistAssignedToFunction']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'newChecklistAssignedToFunction']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <option value=""><?php echo e(__('— Fonction')); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sidebarOrgFunctions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <option value="<?php echo e($fn->id); ?>"><?php echo e($fn->name); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36)): ?>
<?php $attributes = $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36; ?>
<?php unset($__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36)): ?>
<?php $component = $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36; ?>
<?php unset($__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36); ?>
<?php endif; ?>
                        </template>
                        <input type="date" wire:model="newChecklistDueDate" class="block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm" />
                    </div>
                    <div class="flex gap-2">
                        <button type="button" wire:click="addChecklistItemToTicket" wire:loading.attr="disabled" class="cursor-pointer flex-1 inline-flex items-center justify-center gap-1.5 h-9 rounded-lg bg-[var(--accent)] text-white text-xs font-bold disabled:opacity-70 disabled:cursor-not-allowed">
                            <iconify-icon icon="solar:add-circle-linear" width="14"></iconify-icon>
                            <?php echo e(__('Ajouter')); ?>

                        </button>
                        <button type="button" wire:click="closeAddChecklistForm" class="cursor-pointer h-9 px-3 rounded-lg border border-slate-200 bg-white text-slate-700 text-xs font-medium">
                            <?php echo e(__('Annuler')); ?>

                        </button>
                    </div>
                </div>
            <?php else: ?>
                <button type="button" wire:click="openAddChecklistForm" class="mt-3 w-full cursor-pointer inline-flex items-center justify-center gap-2 h-9 rounded-xl border border-dashed border-slate-300 bg-slate-50 text-slate-600 text-xs font-medium hover:bg-slate-100 transition-colors">
                    <iconify-icon icon="solar:add-circle-linear" width="16"></iconify-icon>
                    <?php echo e(__('Ajouter un élément')); ?>

                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Participants -->
    <div class="rounded-2xl border border-slate-200 bg-white p-3 sm:p-4 shadow-sm min-w-0">
        <div class="flex items-center justify-between gap-2 min-w-0 flex-wrap">
            <div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500"><?php echo e(__('Participants')); ?></div>
                <div class="mt-1 text-sm font-semibold text-slate-900"><?php echo e(count($discussionUsers)); ?> <?php echo e(count($discussionUsers) > 1 ? __('personnes') : __('personne')); ?></div>
            </div>
            <button type="button" @click="addParticipantOpen = true" class="inline-flex items-center gap-2 rounded-xl bg-[var(--accent)] px-3 py-2 text-xs font-bold text-white shadow-sm shadow-[var(--accent-ring)] hover:opacity-90 transition-all">
                <iconify-icon icon="solar:user-plus-bold" width="16"></iconify-icon>
                <?php echo e(__('Ajouter')); ?>

            </button>
        </div>
        <div class="mt-4 space-y-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $discussionUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <div class="h-9 w-9 rounded-full flex items-center justify-center text-sm font-semibold shrink-0" style="background: var(--accent-soft); color: var(--accent);">
                        <?php echo e(strtoupper(mb_substr($u->name ?? '?', 0, 1))); ?>

                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 min-w-0 flex-wrap">
                            <span class="text-sm font-semibold text-slate-900 truncate"><?php echo e($u->name); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($u->id === $creator?->id): ?>
                                <span class="text-[10px] font-bold text-slate-500 bg-white border border-slate-200 px-2 py-0.5 rounded-full shrink-0"><?php echo e(__('Créateur')); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->assignees->contains('id', $u->id)): ?>
                                <span class="text-[10px] font-bold text-slate-500 bg-white border border-slate-200 px-2 py-0.5 rounded-full shrink-0"><?php echo e(__('Assigné')); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php $memberRole = $orgMemberRoles[$u->id] ?? null; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($memberRole && $memberRole !== 'member'): ?>
                                <span class="text-[10px] font-bold text-[var(--accent)] bg-[var(--accent-soft)] px-2 py-0.5 rounded-full shrink-0"><?php echo e($roleLabels[$memberRole] ?? $memberRole); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="text-xs text-slate-500 truncate"><?php echo e($u->email ?? ''); ?></div>
                    </div>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($u->id !== $creator?->id && !$ticket->assignees->contains('id', $u->id)): ?>
                        <?php if(($canAssignTicket ?? false) || (auth()->id() && (int)$u->id === (int)auth()->id())): ?>
                            <button type="button" @click="$dispatch('confirm-action', { title: '<?php echo e(__('Retirer')); ?>', message: '<?php echo e(__('Retirer ce participant de la discussion ?')); ?>', confirmLabel: '<?php echo e(__('Retirer')); ?>', variant: 'danger', onConfirm: () => $wire.removeParticipant(<?php echo e($u->id); ?>) })" class="shrink-0 text-slate-400 hover:text-red-500 transition-colors" title="<?php echo e(__('Retirer')); ?>">
                                <iconify-icon icon="solar:close-circle-linear" width="18"></iconify-icon>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>

    <!-- Fichiers -->
    <div class="rounded-2xl border border-slate-200 bg-white p-3 sm:p-4 shadow-sm min-w-0">
        <div class="flex items-center justify-between">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500"><?php echo e(__('Fichiers')); ?></div>
            <div class="text-xs font-semibold text-slate-500"><?php echo e($allAttachments->count()); ?></div>
        </div>
        <div class="mt-3 space-y-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $allAttachments->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <?php echo $__env->make('livewire.tickets.partials.attachment-link', ['att' => $att, 'variant' => 'theirs'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <p class="text-sm text-slate-400"><?php echo e(__('Aucune pièce jointe.')); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <!-- Archive -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canArchive ?? false): ?>
    <div class="rounded-2xl border border-slate-200 bg-white p-3 sm:p-4 shadow-sm min-w-0">
        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-3"><?php echo e(__('Archive')); ?></div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticket->archived_at): ?>
            <div class="mb-3 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                <div class="flex items-start gap-2">
                    <iconify-icon icon="solar:archive-bold-duotone" width="18"></iconify-icon>
                    <div class="min-w-0">
                        <div class="font-semibold"><?php echo e(__('Ticket archivé')); ?></div>
                        <div class="text-amber-700/80 mt-0.5"><?php echo e(__('Archivé le')); ?> <?php echo e($ticket->archived_at->translatedFormat('d M Y à H:i')); ?></div>
                    </div>
                </div>
            </div>
            <button type="button" wire:click="restoreTicket" class="w-full inline-flex items-center justify-center gap-2 h-10 rounded-xl border border-slate-200 bg-white text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                <iconify-icon icon="solar:restart-bold-duotone" width="18"></iconify-icon>
                <?php echo e(__('Restaurer')); ?>

            </button>
        <?php else: ?>
            <p class="text-sm text-slate-500 mb-3"><?php echo e(__('Archivez ce ticket pour le sortir des listes actives.')); ?></p>
            <button type="button" @click="$dispatch('confirm-action', { title: '<?php echo e(__('Archiver')); ?>', message: '<?php echo e(__('Archiver ce ticket ? Il sera retiré des listes actives.')); ?>', confirmLabel: '<?php echo e(__('Archiver')); ?>', variant: 'warning', onConfirm: () => $wire.archiveTicket() })" class="w-full inline-flex items-center justify-center gap-2 h-10 rounded-xl bg-slate-900 text-sm font-bold text-white hover:bg-slate-800 transition-colors">
                <iconify-icon icon="solar:archive-bold-duotone" width="18"></iconify-icon>
                <?php echo e(__('Archiver')); ?>

            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDeleteTicket ?? false): ?>
    <!-- Suppression (soft delete) -->
    <div class="rounded-2xl border border-red-100 bg-red-50/50 p-3 sm:p-4 shadow-sm min-w-0">
        <div class="text-[11px] font-bold uppercase tracking-wider text-red-600 mb-3"><?php echo e(__('Supprimer le ticket')); ?></div>
        <p class="text-sm text-slate-600 mb-3"><?php echo e(__('Le ticket sera masqué des listes. La suppression peut être annulée par un administrateur.')); ?></p>
        <button
            type="button"
            x-on:click="$dispatch('open-modal', 'confirm-delete-ticket')"
            class="w-full inline-flex items-center justify-center gap-2 h-10 rounded-xl bg-red-600 text-sm font-bold text-white hover:bg-red-700 transition-colors"
        >
            <iconify-icon icon="solar:trash-bin-trash-bold-duotone" width="18"></iconify-icon>
            <?php echo e(__('Supprimer')); ?>

        </button>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="mt-auto rounded-2xl border border-slate-200 bg-slate-50 p-3 sm:p-4 text-xs text-slate-500 text-center min-w-0">
        <?php echo e(__('Créé')); ?> <?php echo e($ticket->created_at?->translatedFormat('d M H:i') ?? '—'); ?> · <?php echo e(__('Mis à jour')); ?> <?php echo e($lastActivity?->diffForHumans() ?? '—'); ?>

    </div>
</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/livewire/tickets/partials/discussion-sidebar-content.blade.php ENDPATH**/ ?>