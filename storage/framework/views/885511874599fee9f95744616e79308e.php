<?php
    $statusLabel = function (string $status): array {
        return match ($status) {
            'open' => [__('tickets.status.open'), 'solar:bolt-circle-bold-duotone'],
            'in_progress' => [__('tickets.status.in_progress'), 'solar:clock-circle-bold-duotone'],
            'pending' => [__('tickets.status.pending'), 'solar:hourglass-bold-duotone'],
            'resolved' => [__('tickets.status.resolved'), 'solar:check-circle-bold-duotone'],
            'closed' => [__('tickets.status.closed'), 'solar:lock-keyhole-bold-duotone'],
            default => [__('tickets.status.' . $status) ?: ucfirst(str_replace('_', ' ', $status)), 'solar:question-circle-bold-duotone'],
        };
    };

    $statusPill = function (string $status): array {
        return match ($status) {
            'open' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'border' => 'border-red-100/80'],
            'in_progress' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'border' => 'border-blue-100/80'],
            'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'border' => 'border-amber-100/80'],
            'resolved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-100/80'],
            'closed' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'border' => 'border-slate-100/80'],
            default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'border' => 'border-slate-100/80'],
        };
    };

    $priorityMeta = function (?int $level): array {
        if ($level === null) {
            return ['label' => '—', 'dot' => 'bg-slate-300', 'text' => 'text-slate-500'];
        }

        return match (true) {
            $level >= 4 => ['label' => __('Critique'), 'dot' => 'bg-red-500', 'text' => 'text-red-600'],
            $level === 3 => ['label' => __('Haute'), 'dot' => 'bg-amber-500', 'text' => 'text-amber-600'],
            $level === 2 => ['label' => __('Moyenne'), 'dot' => 'bg-blue-500', 'text' => 'text-blue-600'],
            default => ['label' => __('Basse'), 'dot' => 'bg-emerald-500', 'text' => 'text-emerald-600'],
        };
    };

    $boxKey = $box ?? 'active';
    $isActive = fn (string $k) => ($viewKey ?? 'all') === $k;
?>

<div
    class="w-full max-w-full min-w-0 mx-auto"
    x-data="{
        dragId: null,
        mobileFilters: false,
        sidebarOpen: <?php if ((object) ('sidebarOpen') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('sidebarOpen'->value()); ?>')<?php echo e('sidebarOpen'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('sidebarOpen'); ?>')<?php endif; ?>.live,
    }"
    x-effect="document.body.classList.toggle('overflow-hidden', mobileFilters)"
    x-init="window.addEventListener('resize', () => { if (window.innerWidth >= 1024) mobileFilters = false })"
    x-on:livewire:navigating.window="mobileFilters = false"
>
    
    <div class="page-header">
        <div class="min-w-0">
            <h1 class="page-title"><?php echo e(__('pages.tickets.title')); ?></h1>
            <p class="page-subtitle">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($boxKey === 'trash'): ?>
                    <?php echo e(__('pages.tickets.subtitle_trash')); ?>

                <?php elseif($boxKey === 'archived'): ?>
                    <?php echo e(__('pages.tickets.subtitle_archived')); ?>

                <?php else: ?>
                    <?php echo e(__('pages.tickets.subtitle')); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </p>
        </div>

        <div class="page-actions">
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($boxKey !== 'trash'): ?>
                <div class="view-toggle">
                    <button type="button" wire:click="setDisplayMode('list')" wire:loading.attr="disabled" wire:target="setDisplayMode,setBox,setView,setSource,group,search,status,priority,resetFilters"
                        class="view-toggle-btn <?php echo e(($displayMode ?? 'list') === 'list' ? 'view-toggle-btn-active' : 'view-toggle-btn-default'); ?>">
                        <iconify-icon icon="solar:list-bold" width="16"></iconify-icon>
                    </button>
                    <button type="button" wire:click="setDisplayMode('kanban')" wire:loading.attr="disabled" wire:target="setDisplayMode,setBox,setView,setSource,group,search,status,priority,resetFilters"
                        class="view-toggle-btn <?php echo e(($displayMode ?? 'list') === 'kanban' ? 'view-toggle-btn-active' : 'view-toggle-btn-default'); ?>">
                        <iconify-icon icon="solar:widget-4-bold" width="16"></iconify-icon>
                    </button>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <button type="button"
                class="hidden lg:inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 hover:text-slate-900 transition-all"
                @click="sidebarOpen = ! sidebarOpen">
                <iconify-icon icon="solar:sidebar-minimalistic-bold-duotone" width="18"></iconify-icon>
            </button>

            
            <button type="button"
                class="lg:hidden inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 transition-all touch-target sm:min-h-0 sm:min-w-0"
                @click="mobileFilters = true">
                <iconify-icon icon="solar:filter-bold-duotone" width="18"></iconify-icon>
                <span class="hidden sm:inline"><?php echo e(__('Filtres')); ?></span>
            </button>

            
            <a href="<?php echo e(route('tickets.create')); ?>" wire:navigate
               class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2.5 sm:px-4 text-sm font-semibold text-white shadow-lg shadow-[var(--accent-ring)] hover:opacity-90 transition-all transform hover:-translate-y-0.5 touch-target sm:min-h-0 sm:min-w-0"
               style="background-color: var(--accent);">
                <iconify-icon icon="solar:add-circle-bold" width="18"></iconify-icon>
                <span class="hidden sm:inline"><?php echo e(__('pages.tickets.new_ticket')); ?></span>
            </a>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($activeGroup ?? null) || ($group ?? '') === 'none'): ?>
        <div class="flex items-center gap-3 mb-5 px-4 py-3 rounded-xl bg-white" style="border: 1px solid #f1f5f9;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeGroup ?? null): ?>
                <span class="h-3 w-3 rounded-full shrink-0" style="background-color: <?php echo e($activeGroup->color ?? 'var(--accent)'); ?>;"></span>
                <span class="text-sm font-bold text-slate-900"><?php echo e($activeGroup->name); ?></span>
            <?php else: ?>
                <iconify-icon icon="solar:minus-circle-bold-duotone" width="16" class="text-slate-400 shrink-0"></iconify-icon>
                <span class="text-sm font-bold text-slate-900"><?php echo e(__('pages.groups.ungrouped')); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <span class="text-slate-200">|</span>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" type="button" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-slate-700 transition-colors">
                    <?php echo e(__('pages.groups.switch_group')); ?>

                    <iconify-icon icon="solar:alt-arrow-down-linear" width="12"></iconify-icon>
                </button>
                <div x-show="open" @click.away="open = false" x-transition
                     class="absolute left-0 top-full mt-1 w-48 rounded-xl border border-slate-100 bg-white shadow-lg z-50 py-1">
                    <button type="button" wire:click="$set('group', '')" wire:loading.attr="disabled" wire:target="group" @click="open = false"
                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                        <iconify-icon icon="solar:layers-bold-duotone" width="14"></iconify-icon>
                        <?php echo e(__('pages.tickets.all_groups')); ?>

                    </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = collect($ticketGroups ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <button type="button" wire:click="$set('group', '<?php echo e($tg->id); ?>')" wire:loading.attr="disabled" wire:target="group" @click="open = false"
                                class="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                            <span class="h-2.5 w-2.5 rounded-full shrink-0" style="background-color: <?php echo e($tg->color ?? 'var(--accent)'); ?>;"></span>
                            <?php echo e($tg->name); ?>

                        </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <button type="button" wire:click="$set('group', 'none')" wire:loading.attr="disabled" wire:target="group" @click="open = false"
                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                        <iconify-icon icon="solar:minus-circle-bold-duotone" width="14" class="text-slate-400"></iconify-icon>
                        <?php echo e(__('pages.tickets.no_group')); ?>

                    </button>
                </div>
            </div>
            <a href="<?php echo e(route('tickets.index')); ?>" wire:navigate class="ml-auto inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-[var(--accent)] transition-colors">
                <iconify-icon icon="solar:close-circle-linear" width="14"></iconify-icon>
                <?php echo e(__('pages.groups.all_tickets')); ?>

            </a>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6 sm:mb-8 lg:grid-cols-4">
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('Ouverts')); ?></span>
                    <div class="stat-card-value"><?php echo e($stats['open'] ?? 0); ?></div>
                </div>
                <div class="stat-card-icon bg-red-50 text-red-500">
                    <iconify-icon icon="solar:bolt-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('En cours')); ?></span>
                    <div class="stat-card-value"><?php echo e($stats['in_progress'] ?? 0); ?></div>
                </div>
                <div class="stat-card-icon bg-blue-50 text-blue-500">
                    <iconify-icon icon="solar:clock-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('En attente')); ?></span>
                    <div class="stat-card-value"><?php echo e($stats['pending'] ?? 0); ?></div>
                </div>
                <div class="stat-card-icon bg-amber-50 text-amber-500">
                    <iconify-icon icon="solar:hourglass-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex justify-between items-start gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('Résolus (7j)')); ?></span>
                    <div class="stat-card-value"><?php echo e($stats['resolved_7d'] ?? 0); ?></div>
                </div>
                <div class="stat-card-icon bg-emerald-50 text-emerald-500">
                    <iconify-icon icon="solar:check-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    
    <div class="flex gap-6 lg:gap-8 min-w-0">

        
        <div class="hidden lg:block shrink-0 transition-all duration-300"
             x-show="sidebarOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-x-4 w-0"
             x-transition:enter-end="opacity-100 translate-x-0 w-[16rem]"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 w-[16rem]"
             x-transition:leave-end="opacity-0 w-0"
             style="width: 16rem;">
            <div class="sticky top-24 space-y-4 w-[16rem]">
                
                <div class="sidebar-panel">
                    <div class="p-2">
                        <div class="tab-bar">
                            <button type="button" wire:click="setBox('active')" wire:loading.attr="disabled" wire:target="setBox,setView,setSource,group,search,status,priority,resetFilters"
                                class="tab-bar-item <?php echo e($boxKey === 'active' ? 'tab-bar-item-active' : 'tab-bar-item-default'); ?>">
                                <span class="inline-flex items-center gap-1.5 justify-center w-full truncate">
                                    <iconify-icon icon="solar:ticket-bold-duotone" width="15" class="shrink-0"></iconify-icon>
                                    <span class="truncate"><?php echo e(__('pages.tickets.active')); ?></span>
                                </span>
                            </button>
                            <button type="button" wire:click="setBox('archived')" wire:loading.attr="disabled" wire:target="setBox,setView,setSource,group,search,status,priority,resetFilters"
                                class="tab-bar-item <?php echo e($boxKey === 'archived' ? 'tab-bar-item-active' : 'tab-bar-item-default'); ?>">
                                <span class="inline-flex items-center gap-1.5 justify-center w-full truncate">
                                    <iconify-icon icon="solar:archive-bold-duotone" width="15" class="shrink-0"></iconify-icon>
                                    <span class="truncate"><?php echo e(__('pages.tickets.archived')); ?></span>
                                </span>
                            </button>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isStaff ?? false): ?>
                            <button type="button" wire:click="setBox('trash')" wire:loading.attr="disabled" wire:target="setBox,setView,setSource,group,search,status,priority,resetFilters"
                                class="tab-bar-item <?php echo e($boxKey === 'trash' ? 'tab-bar-item-active' : 'tab-bar-item-default'); ?>">
                                <span class="inline-flex items-center gap-1.5 justify-center w-full truncate">
                                    <iconify-icon icon="solar:trash-bin-trash-bold-duotone" width="15" class="shrink-0"></iconify-icon>
                                    <span class="truncate"><?php echo e(__('pages.tickets.trash')); ?></span>
                                </span>
                            </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>

                
                <div class="sidebar-panel">
                    <div class="sidebar-panel-header">
                        <h3><?php echo e(__('pages.tickets.quick_views')); ?></h3>
                    </div>
                    <div class="sidebar-panel-body">
                        <button type="button" wire:click="setView('all')" wire:loading.attr="disabled" wire:target="setBox,setView,setSource,group,search,status,priority,resetFilters"
                            class="sidebar-item <?php echo e($isActive('all') ? 'sidebar-item-active' : 'sidebar-item-default'); ?>">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:layers-bold-duotone" width="17"></iconify-icon>
                                <?php echo e(__('Tous les tickets')); ?>

                            </span>
                            <span class="sidebar-badge <?php echo e($isActive('all') ? 'sidebar-badge-active' : 'sidebar-badge-default'); ?>"><?php echo e($viewCounts['all'] ?? 0); ?></span>
                        </button>
                        <button type="button" wire:click="setView('created_by_me')" wire:loading.attr="disabled" wire:target="setBox,setView,setSource,group,search,status,priority,resetFilters"
                            class="sidebar-item <?php echo e($isActive('created_by_me') ? 'sidebar-item-active' : 'sidebar-item-default'); ?>">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:pen-bold-duotone" width="17"></iconify-icon>
                                <?php echo e(__('Créés par moi')); ?>

                            </span>
                            <span class="sidebar-badge <?php echo e($isActive('created_by_me') ? 'sidebar-badge-active' : 'sidebar-badge-default'); ?>"><?php echo e($viewCounts['created_by_me'] ?? 0); ?></span>
                        </button>
                        <button type="button" wire:click="setView('assigned_to_me')" wire:loading.attr="disabled" wire:target="setBox,setView,setSource,group,search,status,priority,resetFilters"
                            class="sidebar-item <?php echo e($isActive('assigned_to_me') ? 'sidebar-item-active' : 'sidebar-item-default'); ?>">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:user-check-bold-duotone" width="17"></iconify-icon>
                                <?php echo e(__('Assignés à moi')); ?>

                            </span>
                            <span class="sidebar-badge <?php echo e($isActive('assigned_to_me') ? 'sidebar-badge-active' : 'sidebar-badge-default'); ?>"><?php echo e($viewCounts['assigned_to_me'] ?? 0); ?></span>
                        </button>
                        <button type="button" wire:click="setView('high_priority')" wire:loading.attr="disabled" wire:target="setBox,setView,setSource,group,search,status,priority,resetFilters"
                            class="sidebar-item <?php echo e($isActive('high_priority') ? 'sidebar-item-active' : 'sidebar-item-default'); ?>">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:danger-triangle-bold-duotone" width="17"></iconify-icon>
                                <?php echo e(__('Haute priorité')); ?>

                            </span>
                            <span class="sidebar-badge <?php echo e($isActive('high_priority') ? 'sidebar-badge-active' : 'sidebar-badge-default'); ?>"><?php echo e($viewCounts['high_priority'] ?? 0); ?></span>
                        </button>
                    </div>
                </div>

                
                <div class="sidebar-panel">
                    <div class="sidebar-panel-header">
                        <h3><?php echo e(__('pages.tickets.source')); ?></h3>
                    </div>
                    <div class="sidebar-panel-body">
                        <button type="button" wire:click="setSource('all')" wire:loading.attr="disabled" wire:target="setBox,setView,setSource,group,search,status,priority,resetFilters"
                            class="sidebar-item <?php echo e(($source ?? 'all') === 'all' ? 'sidebar-item-active' : 'sidebar-item-default'); ?>">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:layers-bold-duotone" width="17"></iconify-icon>
                                <?php echo e(__('pages.tickets.source_all')); ?>

                            </span>
                            <span class="sidebar-badge <?php echo e(($source ?? 'all') === 'all' ? 'sidebar-badge-active' : 'sidebar-badge-default'); ?>"><?php echo e(($viewCounts['all'] ?? 0)); ?></span>
                        </button>
                        <button type="button" wire:click="setSource('from_form')" wire:loading.attr="disabled" wire:target="setBox,setView,setSource,group,search,status,priority,resetFilters"
                            class="sidebar-item <?php echo e(($source ?? 'all') === 'from_form' ? 'sidebar-item-active' : 'sidebar-item-default'); ?>">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:document-text-bold-duotone" width="17"></iconify-icon>
                                <?php echo e(__('pages.tickets.source_from_form')); ?>

                            </span>
                            <span class="sidebar-badge <?php echo e(($source ?? 'all') === 'from_form' ? 'sidebar-badge-active' : 'sidebar-badge-default'); ?>"><?php echo e($viewCounts['from_form'] ?? 0); ?></span>
                        </button>
                        <button type="button" wire:click="setSource('from_platform')" wire:loading.attr="disabled" wire:target="setBox,setView,setSource,group,search,status,priority,resetFilters"
                            class="sidebar-item <?php echo e(($source ?? 'all') === 'from_platform' ? 'sidebar-item-active' : 'sidebar-item-default'); ?>">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:pen-new-square-bold-duotone" width="17"></iconify-icon>
                                <?php echo e(__('pages.tickets.source_from_platform')); ?>

                            </span>
                            <span class="sidebar-badge <?php echo e(($source ?? 'all') === 'from_platform' ? 'sidebar-badge-active' : 'sidebar-badge-default'); ?>"><?php echo e($viewCounts['from_platform'] ?? 0); ?></span>
                        </button>
                    </div>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($ticketGroups ?? collect())->isNotEmpty()): ?>
                <div class="sidebar-panel">
                    <div class="sidebar-panel-header">
                        <h3><?php echo e(__('pages.tickets.groups')); ?></h3>
                    </div>
                    <div class="sidebar-panel-body">
                        <button type="button" wire:click="$set('group', '')"
                            class="sidebar-item <?php echo e(($group ?? '') === '' ? 'sidebar-item-active' : 'sidebar-item-default'); ?>">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:layers-bold-duotone" width="17"></iconify-icon>
                                <?php echo e(__('pages.tickets.all_groups')); ?>

                            </span>
                        </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $ticketGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <button type="button" wire:click="$set('group', '<?php echo e($tg->id); ?>')"
                                class="sidebar-item <?php echo e(($group ?? '') === (string) $tg->id ? 'sidebar-item-active' : 'sidebar-item-default'); ?>">
                                <span class="flex items-center gap-2.5">
                                    <span class="h-2.5 w-2.5 rounded-full shrink-0" style="background-color: <?php echo e($tg->color ?? 'var(--accent)'); ?>;"></span>
                                    <?php echo e($tg->name); ?>

                                </span>
                                <span class="sidebar-badge <?php echo e(($group ?? '') === (string) $tg->id ? 'sidebar-badge-active' : 'sidebar-badge-default'); ?>"><?php echo e($groupCounts[$tg->id] ?? 0); ?></span>
                            </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <button type="button" wire:click="$set('group', 'none')"
                            class="sidebar-item <?php echo e(($group ?? '') === 'none' ? 'sidebar-item-active' : 'sidebar-item-default'); ?>">
                            <span class="flex items-center gap-2.5">
                                <iconify-icon icon="solar:minus-circle-bold-duotone" width="17" class="text-slate-400"></iconify-icon>
                                <?php echo e(__('pages.tickets.no_group')); ?>

                            </span>
                        </button>
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="flex-1 min-w-0">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($loadStage ?? 0) >= 2): ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($displayMode ?? 'list') === 'kanban' && $boxKey !== 'trash'): ?>
                
                <div class="content-card h-[calc(100vh-14rem)] sm:h-[calc(100vh-12rem)] min-h-[400px] flex flex-col">
                    <div class="shrink-0 p-3 sm:p-4 border-b border-slate-100/80 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between bg-slate-50/30">
                        <h2 class="text-sm font-bold text-slate-900"><?php echo e(__('pages.tickets.kanban_board')); ?></h2>
                        <div class="text-xs text-slate-400 hidden sm:block"><?php echo e(__('pages.tickets.drag_to_change_status')); ?></div>
                    </div>
                    <div wire:loading.flex wire:target="moveTicket,setDisplayMode,setBox,setView,setSource,group,search,status,priority,resetFilters" class="shrink-0 px-4 py-2 text-xs text-slate-500 items-center gap-2 border-b border-slate-50 bg-white/70">
                        <iconify-icon icon="solar:refresh-linear" width="14" class="animate-spin"></iconify-icon>
                        <?php echo e(__('Chargement...')); ?>

                    </div>
                    <div class="flex-1 min-h-0 min-w-0 p-3 sm:p-4 overflow-x-auto overflow-y-hidden custom-scrollbar scroll-touch">
                        <div wire:loading.class="opacity-60 pointer-events-none" wire:target="moveTicket,setDisplayMode,setBox,setView,setSource,group,search,status,priority,resetFilters" class="flex gap-3 sm:gap-4 h-full min-w-max pb-2 transition-opacity duration-150">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($statusColumns ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $colStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <?php
                                    [$colLabel, $colIcon] = $statusLabel($colStatus);
                                    $colPill = $statusPill($colStatus);
                                    $cards = $kanbanTickets[$colStatus] ?? [];
                                ?>
                                <div class="w-[272px] sm:w-[300px] kanban-column"
                                    @dragover.prevent
                                    @drop.prevent="if ($root.dragId) { $wire.moveTicket($root.dragId, '<?php echo e($colStatus); ?>'); $root.dragId = null; }">
                                    <div class="kanban-column-header">
                                        <span class="pill-badge <?php echo e($colPill['bg']); ?> <?php echo e($colPill['text']); ?> <?php echo e($colPill['border']); ?>">
                                            <?php echo e($colLabel); ?>

                                        </span>
                                        <span class="text-xs font-bold text-slate-300"><?php echo e(count($cards)); ?></span>
                                    </div>
                                    <div class="p-2.5 space-y-2.5 overflow-y-auto custom-scrollbar flex-1">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                            <?php
                                                $prio = $priorityMeta($t->priority?->level);
                                                $prog = $checklistProgress[$t->id] ?? null;
                                                $pct = $prog && (int) $prog->total > 0 ? (int) round(100 * (int) $prog->done / (int) $prog->total) : null;
                                            ?>
                                            <div x-data="{ dragging: false }"
                                                class="kanban-card group"
                                                draggable="true"
                                                @mousedown="dragging = false"
                                                @mousemove="dragging = true"
                                                @click="if (!dragging) Livewire.navigate('<?php echo e($t->public_id ? url('/tickets/' . e($t->public_id)) : '#'); ?>')"
                                                @dragstart="$root.dragId = <?php echo e((int) $t->id); ?>"
                                                @dragend="$root.dragId = null">
                                                <div class="flex justify-between items-start mb-2">
                                                    <span class="text-[11px] font-mono font-bold text-slate-400"><?php echo e($t->shortReference()); ?></span>
                                                    <span class="h-2 w-2 rounded-full <?php echo e($prio['dot']); ?>" title="<?php echo e($prio['label']); ?>"></span>
                                                </div>
                                                <h4 class="text-sm font-bold text-slate-900 mb-2 line-clamp-2 group-hover:text-[var(--accent)] transition-colors leading-snug"><?php echo e($t->subject); ?></h4>
                                                <div class="flex items-center gap-1.5 mb-2 flex-wrap">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t->group): ?>
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-medium border" style="background-color: <?php echo e($t->group->color ?? 'var(--accent)'); ?>15; color: <?php echo e($t->group->color ?? 'var(--accent)'); ?>; border-color: <?php echo e($t->group->color ?? 'var(--accent)'); ?>30;">
                                                            <span class="h-1.5 w-1.5 rounded-full" style="background-color: <?php echo e($t->group->color ?? 'var(--accent)'); ?>;"></span>
                                                            <?php echo e($t->group->name); ?>

                                                        </span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t->formResponse): ?>
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-violet-50 text-violet-600 border border-violet-100/80">
                                                            <iconify-icon icon="solar:document-text-bold-duotone" width="10"></iconify-icon>
                                                            <?php echo e(__('pages.tickets.source_label_form')); ?>

                                                        </span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t->sla_policy_id): ?>
                                                        <?php if (isset($component)) { $__componentOriginalf8537846ecb099bfba89af16a4fcaefa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8537846ecb099bfba89af16a4fcaefa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sla-badge','data' => ['status' => $t->slaFirstResponseStatus(),'type' => 'fr']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sla-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($t->slaFirstResponseStatus()),'type' => 'fr']); ?>
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
                                                        <?php if (isset($component)) { $__componentOriginalf8537846ecb099bfba89af16a4fcaefa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8537846ecb099bfba89af16a4fcaefa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sla-badge','data' => ['status' => $t->slaResolutionStatus(),'type' => 'res']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sla-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($t->slaResolutionStatus()),'type' => 'res']); ?>
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
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t->approval_status === 'pending'): ?>
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200/60">
                                                            <iconify-icon icon="solar:shield-check-linear" width="10"></iconify-icon>
                                                            Validation
                                                        </span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pct !== null): ?>
                                                    <div class="mb-2">
                                                        <div class="flex items-center gap-2">
                                                            <div class="flex-1 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                                                <div class="h-full rounded-full transition-all <?php echo e($pct >= 100 ? 'bg-emerald-500' : 'bg-slate-400'); ?>" style="width: <?php echo e($pct); ?>%"></div>
                                                            </div>
                                                            <span class="text-[10px] font-bold <?php echo e($pct >= 100 ? 'text-emerald-600' : 'text-slate-400'); ?>"><?php echo e($pct); ?>%</span>
                                                        </div>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-50">
                                                    <div class="flex items-center gap-2">
                                                        <?php if (isset($component)) { $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.avatar','data' => ['name' => $t->creator?->name ?? 'U','size' => 'h-5 w-5','class' => 'ring-1 ring-white shadow-sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($t->creator?->name ?? 'U'),'size' => 'h-5 w-5','class' => 'ring-1 ring-white shadow-sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $attributes = $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $component = $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
                                                        <span class="text-[11px] text-slate-500 truncate max-w-[100px]"><?php echo e($t->creator?->name); ?></span>
                                                    </div>
                                                    <span class="text-[10px] text-slate-300"><?php echo e($t->updated_at?->diffForHumans()); ?></span>
                                                </div>
                                            </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            <div class="py-8 text-center text-xs text-slate-300 italic"><?php echo e(__('pages.tickets.empty_column')); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                
                <?php
                    $ticketStatusFilterOptions = [
                        ['value' => '', 'label' => __('pages.dashboard.status')],
                        ['value' => 'open', 'label' => __('Ouvert')],
                        ['value' => 'in_progress', 'label' => __('En cours')],
                        ['value' => 'pending', 'label' => __('En attente')],
                        ['value' => 'resolved', 'label' => __('Résolu')],
                        ['value' => 'closed', 'label' => __('Fermé')],
                    ];
                    $ticketStatusFilterLabel = $status === ''
                        ? __('pages.dashboard.status')
                        : (collect($ticketStatusFilterOptions)->firstWhere('value', $status)['label'] ?? $status);
                    $ticketPriorityFilterOptions = [['value' => '', 'label' => __('pages.dashboard.priority')]];
                    foreach ($priorities as $p) {
                        $ticketPriorityFilterOptions[] = ['value' => (string) $p->id, 'label' => $p->name];
                    }
                    $ticketPriorityFilterLabel = $priority === ''
                        ? __('pages.dashboard.priority')
                        : ($priorities->firstWhere('id', (int) $priority)?->name ?? $priority);
                ?>
                <div class="content-card">
                    
                    <div class="filter-bar">
                        <div class="filter-bar-row">
                            <div class="flex-1 min-w-0 relative">
                                <iconify-icon icon="solar:magnifer-linear" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none" width="18"></iconify-icon>
                                <input type="text" class="filter-search"
                                    placeholder="<?php echo e(__('pages.tickets.search_placeholder')); ?>"
                                    wire:model.live.debounce.500ms="search"
                                    wire:loading.attr="disabled"
                                    wire:target="search,status,priority,resetFilters" />
                            </div>
                            <div class="filter-controls">
                                <div class="w-full min-w-0 sm:w-36 flex-1 sm:flex-none">
                                    <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['options' => $ticketStatusFilterOptions,'label' => $ticketStatusFilterLabel,'selectedValue' => $status,'wire:model' => 'status','wire:loading.attr' => 'disabled','wire:target' => 'search,status,priority,resetFilters']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ticketStatusFilterOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ticketStatusFilterLabel),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($status),'wire:model' => 'status','wire:loading.attr' => 'disabled','wire:target' => 'search,status,priority,resetFilters']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

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
                                <div class="w-full min-w-0 sm:w-36 flex-1 sm:flex-none">
                                    <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['options' => $ticketPriorityFilterOptions,'label' => $ticketPriorityFilterLabel,'selectedValue' => $priority,'wire:model' => 'priority','wire:loading.attr' => 'disabled','wire:target' => 'search,status,priority,resetFilters']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ticketPriorityFilterOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ticketPriorityFilterLabel),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($priority),'wire:model' => 'priority','wire:loading.attr' => 'disabled','wire:target' => 'search,status,priority,resetFilters']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

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
                                <button wire:click="resetFilters" wire:loading.attr="disabled" wire:target="resetFilters,search,status,priority" class="filter-reset touch-target sm:min-h-0">
                                    <iconify-icon icon="solar:restart-linear" width="16" class="text-slate-400"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div wire:loading.flex wire:target="search,status,priority,resetFilters,setDisplayMode,setBox,setView,setSource,group,nextPage,previousPage,gotoPage,setPage" class="px-4 py-2 text-xs text-slate-500 items-center gap-2 border-t border-slate-50 bg-white/70">
                        <iconify-icon icon="solar:refresh-linear" width="14" class="animate-spin"></iconify-icon>
                        <?php echo e(__('Chargement...')); ?>

                    </div>

                    
                    <div wire:loading.class="opacity-60 pointer-events-none" wire:target="search,status,priority,resetFilters,setDisplayMode,setBox,setView,setSource,group,nextPage,previousPage,gotoPage,setPage,restoreFromTrash,forceDeleteTicket" class="responsive-table-wrap scroll-touch transition-opacity duration-150">
                        <table class="data-table min-w-[680px] sm:min-w-[760px]">
                            <thead>
                                <tr>
                                    <th class="min-w-[200px] sm:min-w-[260px] lg:min-w-[300px]"><?php echo e(__('pages.dashboard.subject')); ?></th>
                                    <th><?php echo e(__('Catégorie')); ?></th>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($ticketGroups ?? collect())->isNotEmpty()): ?>
                                    <th><?php echo e(__('pages.tickets.groups')); ?></th>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <th><?php echo e(__('pages.dashboard.priority')); ?></th>
                                    <th><?php echo e(__('pages.dashboard.status')); ?></th>
                                    <th class="text-right"><?php echo e(__('pages.tickets.activity')); ?></th>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($boxKey === 'trash'): ?>
                                        <th class="text-right"><?php echo e(__('pages.tickets.actions')); ?></th>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <?php
                                        [$label, $icon] = $statusLabel($t->status->value);
                                        $pill = $statusPill($t->status->value);
                                        $prio = $priorityMeta($t->priority?->level);
                                    ?>
                                    <tr class="<?php echo e($boxKey !== 'trash' ? 'cursor-pointer' : ''); ?>" <?php if($boxKey !== 'trash' && $t->public_id): ?> onclick="Livewire.navigate('<?php echo e(url('/tickets/' . e($t->public_id))); ?>')" <?php endif; ?>>
                                        <td class="min-w-[200px] sm:min-w-[260px] lg:min-w-[300px]">
                                            <div class="flex items-start sm:items-center gap-3 sm:gap-4 min-w-0">
                                                <span class="shrink-0 inline-flex items-center justify-center rounded-lg px-2 py-1.5 sm:px-2.5 sm:py-1.5 text-[11px] sm:text-xs font-semibold font-mono tracking-tight bg-[var(--accent-soft)] text-[var(--accent)]">
                                                    <?php echo e($t->shortReference()); ?>

                                                </span>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex flex-wrap items-baseline gap-2 gap-y-1">
                                                        <span class="text-sm font-bold text-slate-900 group-hover:text-[var(--accent)] transition-colors break-words line-clamp-2 leading-snug"><?php echo e($t->subject); ?></span>
                                                        <?php $prog = $checklistProgress[$t->id] ?? null; ?>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prog && (int) $prog->total > 0): ?>
                                                            <?php $pct = (int) round(100 * (int) $prog->done / (int) $prog->total); ?>
                                                            <span class="shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold <?php echo e($pct >= 100 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'); ?>">
                                                                <iconify-icon icon="solar:checklist-minimalistic-linear" width="11"></iconify-icon>
                                                                <?php echo e($pct); ?>%
                                                            </span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                    <div class="text-xs text-slate-400 mt-1 flex items-center gap-1 flex-wrap">
                                                        <span class="truncate"><?php echo e($t->creator?->name ?? __('pages.tickets.unknown_user')); ?></span>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t->assignees->isNotEmpty()): ?>
                                                            <span class="text-slate-200">·</span>
                                                            <span class="flex items-center gap-1">
                                                                <span class="flex -space-x-1.5">
                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $t->assignees->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[9px] font-bold ring-2 ring-white shrink-0" style="background: var(--accent-soft); color: var(--accent);" title="<?php echo e($a->name); ?>"><?php echo e(strtoupper(mb_substr($a->name, 0, 1))); ?></span>
                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                                </span>
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t->assignees->count() > 3): ?>
                                                                    <span class="text-[10px] font-bold text-slate-400">+<?php echo e($t->assignees->count() - 3); ?></span>
                                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                            </span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-sm text-slate-500 whitespace-nowrap">
                                            <?php echo e($t->category?->name ?? '—'); ?>

                                        </td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($ticketGroups ?? collect())->isNotEmpty()): ?>
                                        <td class="whitespace-nowrap">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t->group): ?>
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-xs font-medium border" style="background-color: <?php echo e($t->group->color ?? 'var(--accent)'); ?>15; color: <?php echo e($t->group->color ?? 'var(--accent)'); ?>; border-color: <?php echo e($t->group->color ?? 'var(--accent)'); ?>30;">
                                                    <span class="h-1.5 w-1.5 rounded-full" style="background-color: <?php echo e($t->group->color ?? 'var(--accent)'); ?>;"></span>
                                                    <?php echo e($t->group->name); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-xs text-slate-300">—</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <span class="h-2 w-2 shrink-0 rounded-full <?php echo e($prio['dot']); ?>"></span>
                                                <span class="text-sm font-medium <?php echo e($prio['text']); ?>"><?php echo e($t->priority?->name ?? $prio['label']); ?></span>
                                            </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t->sla_policy_id): ?>
                                                <div class="flex items-center gap-1 mt-1">
                                                    <?php if (isset($component)) { $__componentOriginalf8537846ecb099bfba89af16a4fcaefa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8537846ecb099bfba89af16a4fcaefa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sla-badge','data' => ['status' => $t->slaFirstResponseStatus(),'type' => 'fr']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sla-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($t->slaFirstResponseStatus()),'type' => 'fr']); ?>
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
                                                    <?php if (isset($component)) { $__componentOriginalf8537846ecb099bfba89af16a4fcaefa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8537846ecb099bfba89af16a4fcaefa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sla-badge','data' => ['status' => $t->slaResolutionStatus(),'type' => 'res']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sla-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($t->slaResolutionStatus()),'type' => 'res']); ?>
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
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="whitespace-nowrap">
                                            <span class="pill-badge <?php echo e($pill['bg']); ?> <?php echo e($pill['text']); ?> <?php echo e($pill['border']); ?>">
                                                <iconify-icon icon="<?php echo e($icon); ?>" width="14"></iconify-icon>
                                                <?php echo e($label); ?>

                                            </span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t->approval_status === 'pending'): ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200/60 ml-1">
                                                    <iconify-icon icon="solar:shield-check-linear" width="11"></iconify-icon>
                                                    Validation
                                                </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="text-right text-sm text-slate-400 whitespace-nowrap">
                                            <?php echo e($boxKey === 'trash' ? ($t->deleted_at?->diffForHumans() ?? '—') : $t->updated_at?->diffForHumans()); ?>

                                        </td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($boxKey === 'trash'): ?>
                                            <td class="text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-2">
                                                    <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'button','wire:click' => 'restoreFromTrash('.e($t->id).')','wireTarget' => 'restoreFromTrash','variant' => 'secondary','spinnerSize' => 'sm','dataRestore' => true,'class' => 'min-h-[44px] sm:min-h-0 sm:py-1.5 !rounded-lg border border-slate-200 text-xs touch-manipulation','loadingLabel' => __('ui.tickets.restoring')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','wire:click' => 'restoreFromTrash('.e($t->id).')','wire-target' => 'restoreFromTrash','variant' => 'secondary','spinner-size' => 'sm','data-restore' => true,'class' => 'min-h-[44px] sm:min-h-0 sm:py-1.5 !rounded-lg border border-slate-200 text-xs touch-manipulation','loading-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.tickets.restoring'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                        <iconify-icon icon="solar:restart-bold-duotone" width="14"></iconify-icon>
                                                        <?php echo e(__('pages.tickets.restore')); ?>

                                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $attributes = $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $component = $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
                                                    <button type="button" @click="$dispatch('confirm-action', { title: '<?php echo e(__('pages.tickets.force_delete')); ?>', message: '<?php echo e(__('pages.tickets.force_delete_confirm')); ?>', confirmLabel: '<?php echo e(__('pages.tickets.force_delete')); ?>', variant: 'danger', onConfirm: () => $wire.forceDeleteTicket(<?php echo e($t->id); ?>) })" class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-2 min-h-[44px] sm:min-h-0 sm:py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100 transition-colors touch-manipulation">
                                                        <iconify-icon icon="solar:trash-bin-trash-bold" width="14"></iconify-icon>
                                                        <?php echo e(__('pages.tickets.force_delete')); ?>

                                                    </button>
                                                </div>
                                            </td>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <tr>
                                        <td colspan="<?php echo e(($boxKey === 'trash' ? 6 : 5) + (($ticketGroups ?? collect())->isNotEmpty() ? 1 : 0)); ?>">
                                            <div class="empty-state">
                                                <div class="empty-state-icon">
                                                    <iconify-icon icon="solar:ticket-linear" width="28" class="text-slate-300"></iconify-icon>
                                                </div>
                                                <p class="empty-state-title"><?php echo e(__('pages.tickets.no_tickets_found')); ?></p>
                                                <p class="empty-state-text"><?php echo e(__('pages.tickets.no_tickets_try_filters')); ?></p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tickets->hasPages()): ?>
                    <div class="px-4 sm:px-6 py-4 border-t border-slate-50 bg-slate-50/20 overflow-x-auto">
                        <?php echo e($tickets->links()); ?>

                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php else: ?>
            
            <div class="content-card">
                <div class="p-4 sm:p-6 animate-pulse space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="h-9 bg-slate-100 rounded-lg w-64"></div>
                        <div class="h-9 bg-slate-100 rounded-lg w-32"></div>
                        <div class="ml-auto h-9 bg-slate-100 rounded-lg w-24"></div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 6; $i++): ?>
                    <div class="flex items-center gap-4 py-3 border-b border-slate-50">
                        <div class="h-4 bg-slate-200 rounded w-16"></div>
                        <div class="flex-1 space-y-1.5">
                            <div class="h-4 bg-slate-100 rounded w-3/4"></div>
                            <div class="h-3 bg-slate-50 rounded w-1/3"></div>
                        </div>
                        <div class="h-6 bg-slate-50 rounded-full w-20"></div>
                        <div class="h-6 bg-slate-50 rounded-full w-16"></div>
                    </div>
                    <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <template x-teleport="body">
    <div x-show="mobileFilters" x-cloak class="lg:hidden fixed inset-0 z-[60]" style="display: none;">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
             @click="mobileFilters = false"
             x-show="mobileFilters"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>
        <div class="absolute left-0 top-0 bottom-0 w-full max-w-[min(100%,20rem)] bg-white shadow-2xl flex flex-col rounded-r-2xl overflow-hidden"
             x-show="mobileFilters"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="-translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="-translate-x-full opacity-0">

            
            <div class="shrink-0 flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid #f1f5f9;">
                <h2 class="text-base font-bold text-slate-900"><?php echo e(__('Filtres')); ?></h2>
                <button @click="mobileFilters = false" class="h-9 w-9 rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors flex items-center justify-center touch-manipulation">
                    <iconify-icon icon="solar:close-circle-bold" width="22"></iconify-icon>
                </button>
            </div>

            
            <div class="flex-1 overflow-y-auto overscroll-contain custom-scrollbar p-4 space-y-4" @touchmove.stop>
                
                <div class="tab-bar">
                    <button type="button" wire:click="setBox('active')" wire:loading.attr="disabled" wire:target="setBox,setView,setSource,group" @click="mobileFilters = false"
                        class="tab-bar-item <?php echo e($boxKey === 'active' ? 'tab-bar-item-active' : 'tab-bar-item-default'); ?>">
                        <?php echo e(__('pages.tickets.active')); ?>

                    </button>
                    <button type="button" wire:click="setBox('archived')" wire:loading.attr="disabled" wire:target="setBox,setView,setSource,group" @click="mobileFilters = false"
                        class="tab-bar-item <?php echo e($boxKey === 'archived' ? 'tab-bar-item-active' : 'tab-bar-item-default'); ?>">
                        <?php echo e(__('pages.tickets.archived')); ?>

                    </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isStaff ?? false): ?>
                    <button type="button" wire:click="setBox('trash')" wire:loading.attr="disabled" wire:target="setBox,setView,setSource,group" @click="mobileFilters = false"
                        class="tab-bar-item <?php echo e($boxKey === 'trash' ? 'tab-bar-item-active' : 'tab-bar-item-default'); ?>">
                        <?php echo e(__('pages.tickets.trash')); ?>

                    </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div>
                    <h3 class="text-[10px] font-bold uppercase tracking-[0.08em] text-slate-400 mb-2 px-1"><?php echo e(__('pages.tickets.quick_views')); ?></h3>
                    <div class="space-y-0.5">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['all' => __('Tous les tickets'), 'created_by_me' => __('Créés par moi'), 'assigned_to_me' => __('Assignés à moi'), 'high_priority' => __('Haute priorité')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vk => $vl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <button type="button" wire:click="setView('<?php echo e($vk); ?>')" wire:loading.attr="disabled" wire:target="setView,setBox,setSource,group" @click="mobileFilters = false"
                                class="sidebar-item <?php echo e($isActive($vk) ? 'sidebar-item-active' : 'sidebar-item-default'); ?>">
                                <span><?php echo e($vl); ?></span>
                                <span class="sidebar-badge <?php echo e($isActive($vk) ? 'sidebar-badge-active' : 'sidebar-badge-default'); ?>"><?php echo e($viewCounts[$vk] ?? 0); ?></span>
                            </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>

                
                <div>
                    <h3 class="text-[10px] font-bold uppercase tracking-[0.08em] text-slate-400 mb-2 px-1"><?php echo e(__('pages.tickets.source')); ?></h3>
                    <div class="space-y-0.5">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['all' => __('pages.tickets.source_all'), 'from_form' => __('pages.tickets.source_from_form'), 'from_platform' => __('pages.tickets.source_from_platform')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sk => $sl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <button type="button" wire:click="setSource('<?php echo e($sk); ?>')" wire:loading.attr="disabled" wire:target="setSource,setView,setBox,group" @click="mobileFilters = false"
                                class="sidebar-item <?php echo e(($source ?? 'all') === $sk ? 'sidebar-item-active' : 'sidebar-item-default'); ?>">
                                <span><?php echo e($sl); ?></span>
                            </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($ticketGroups ?? collect())->isNotEmpty()): ?>
                <div>
                    <h3 class="text-[10px] font-bold uppercase tracking-[0.08em] text-slate-400 mb-2 px-1"><?php echo e(__('pages.tickets.groups')); ?></h3>
                    <div class="space-y-0.5">
                        <button type="button" wire:click="$set('group', '')" wire:loading.attr="disabled" wire:target="group" @click="mobileFilters = false"
                            class="sidebar-item <?php echo e(($group ?? '') === '' ? 'sidebar-item-active' : 'sidebar-item-default'); ?>">
                            <?php echo e(__('pages.tickets.all_groups')); ?>

                        </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $ticketGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <button type="button" wire:click="$set('group', '<?php echo e($tg->id); ?>')" wire:loading.attr="disabled" wire:target="group" @click="mobileFilters = false"
                                class="sidebar-item <?php echo e(($group ?? '') === (string) $tg->id ? 'sidebar-item-active' : 'sidebar-item-default'); ?>">
                                <span class="flex items-center gap-2">
                                    <span class="h-2.5 w-2.5 rounded-full shrink-0" style="background-color: <?php echo e($tg->color ?? 'var(--accent)'); ?>;"></span>
                                    <?php echo e($tg->name); ?>

                                </span>
                                <span class="sidebar-badge sidebar-badge-default"><?php echo e($groupCounts[$tg->id] ?? 0); ?></span>
                            </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
    </template>
</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/livewire/tickets/index.blade.php ENDPATH**/ ?>