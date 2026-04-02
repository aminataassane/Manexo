<div class="space-y-6 pb-12">
    
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900"><?php echo e(__('super_admin.organizations.title')); ?></h1>
            <p class="mt-1 text-sm text-slate-500"><?php echo e(__('super_admin.organizations.subtitle')); ?></p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 shadow-sm">
                <iconify-icon icon="solar:calendar-linear" class="text-slate-400"></iconify-icon>
                <?php echo e(now()->translatedFormat('d F Y')); ?>

            </span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canPlatformManage()): ?>
            <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['wire:click' => 'openCreateModal','wireTarget' => 'openCreateModal','variant' => 'super','class' => 'inline-flex items-center gap-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'openCreateModal','wire-target' => 'openCreateModal','variant' => 'super','class' => 'inline-flex items-center gap-2']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <iconify-icon icon="solar:add-circle-bold" width="18"></iconify-icon>
                <?php echo e(__('super_admin.organizations.create')); ?>

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
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </header>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-center gap-2">
            <iconify-icon icon="solar:check-circle-bold" width="18"></iconify-icon>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <section class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
        <?php
            $kpis = [
                ['label' => __('super_admin.dashboard.total_orgs'),               'value' => $this->stats['total_orgs'],    'color' => 'slate',   'icon' => 'solar:buildings-bold-duotone'],
                ['label' => __('super_admin.dashboard.active_orgs'),              'value' => $this->stats['active_orgs'],   'color' => 'emerald', 'icon' => 'solar:check-circle-bold-duotone'],
                ['label' => __('super_admin.dashboard.suspended_orgs'),           'value' => $this->stats['suspended_orgs'],'color' => 'amber',   'icon' => 'solar:pause-circle-bold-duotone'],
                ['label' => __('super_admin.dashboard.disabled_orgs'),            'value' => $this->stats['disabled_orgs'], 'color' => 'red',     'icon' => 'solar:close-circle-bold-duotone'],
                ['label' => __('super_admin.organizations.stats_total_members'),  'value' => $this->stats['total_members'], 'color' => 'blue',    'icon' => 'solar:users-group-rounded-bold-duotone'],
                ['label' => __('super_admin.organizations.stats_total_tickets'),  'value' => $this->stats['total_tickets'], 'color' => 'violet',  'icon' => 'solar:ticket-bold-duotone'],
            ];
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-<?php echo e($kpi['color']); ?>-50 text-<?php echo e($kpi['color']); ?>-600">
                        <iconify-icon icon="<?php echo e($kpi['icon']); ?>" width="18"></iconify-icon>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-medium text-slate-500 truncate"><?php echo e($kpi['label']); ?></p>
                        <p class="text-lg font-bold text-<?php echo e($kpi['color'] === 'slate' ? 'slate-900' : $kpi['color'] . '-600'); ?> tabular-nums leading-tight"><?php echo e($kpi['value']); ?></p>
                    </div>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </section>

    
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <iconify-icon icon="solar:magnifer-linear" width="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></iconify-icon>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="<?php echo e(__('super_admin.organizations.search_placeholder')); ?>"
                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 placeholder-slate-400 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all"
            />
        </div>
        <?php
            $orgStatusFilterOptions = [
                ['value' => '', 'label' => __('super_admin.organizations.all_statuses')],
                ['value' => 'active', 'label' => __('super_admin.organizations.status_active')],
                ['value' => 'suspended', 'label' => __('super_admin.organizations.status_suspended')],
                ['value' => 'disabled', 'label' => __('super_admin.organizations.status_disabled')],
            ];
            $orgStatusFilterLabel = collect($orgStatusFilterOptions)->firstWhere('value', (string) ($statusFilter ?? ''))['label'] ?? __('super_admin.organizations.all_statuses');
        ?>
        <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['options' => $orgStatusFilterOptions,'label' => $orgStatusFilterLabel,'selectedValue' => $statusFilter ?? '','wire:model.live' => 'statusFilter']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orgStatusFilterOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($orgStatusFilterLabel),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statusFilter ?? ''),'wire:model.live' => 'statusFilter']); ?>
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

    
    <div class="space-y-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_5 = true; $__currentLoopData = $organizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $org): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_5 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <?php
                $status = $org->status ?? 'active';
                $statusConf = match($status) {
                    'active'    => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500'],
                    'suspended' => ['bg' => 'bg-amber-50',   'text' => 'text-amber-700',   'border' => 'border-amber-200',   'dot' => 'bg-amber-500'],
                    'disabled'  => ['bg' => 'bg-red-50',     'text' => 'text-red-700',     'border' => 'border-red-200',     'dot' => 'bg-red-500'],
                    default     => ['bg' => 'bg-slate-50',   'text' => 'text-slate-700',   'border' => 'border-slate-200',   'dot' => 'bg-slate-400'],
                };
                $orgColor = $org->primary_color ?: '#005F02';
            ?>
            <div class="group rounded-2xl border border-slate-200 bg-white shadow-sm transition-all hover:shadow-md hover:border-slate-300">
                <div class="flex items-center gap-4 px-5 py-4">
                    
                    <a href="<?php echo e(route('platform-admin.organizations.show', $org)); ?>" class="shrink-0">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl text-sm font-bold shadow-sm ring-1 ring-black/5 transition-transform group-hover:scale-105"
                             style="background: color-mix(in srgb, <?php echo e($orgColor); ?> 12%, white); color: <?php echo e($orgColor); ?>;">
                            <?php echo e(mb_strtoupper(mb_substr($org->name, 0, 2))); ?>

                        </div>
                    </a>

                    
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <a href="<?php echo e(route('platform-admin.organizations.show', $org)); ?>" class="text-sm font-semibold text-slate-900 hover:text-[#005F02] transition-colors truncate">
                                <?php echo e($org->name); ?>

                            </a>
                            <span class="inline-flex items-center gap-1.5 rounded-full border px-2 py-0.5 text-[10px] font-semibold <?php echo e($statusConf['bg']); ?> <?php echo e($statusConf['text']); ?> <?php echo e($statusConf['border']); ?>">
                                <span class="h-1.5 w-1.5 rounded-full <?php echo e($statusConf['dot']); ?>"></span>
                                <?php echo e(__('super_admin.organizations.status_' . $status)); ?>

                            </span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($org->isArchived()): ?>
                                <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500">
                                    <iconify-icon icon="solar:archive-linear" width="10" class="mr-1"></iconify-icon>
                                    <?php echo e(__('super_admin.organizations.status_archived')); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="mt-1 flex items-center gap-4 text-xs text-slate-500">
                            <span class="font-mono text-slate-400"><?php echo e($org->slug); ?></span>
                            <span class="hidden sm:inline-flex items-center gap-1">
                                <iconify-icon icon="solar:users-group-rounded-linear" width="13" class="text-slate-400"></iconify-icon>
                                <?php echo e($org->memberships_count); ?> <?php echo e(__('super_admin.organizations.col_members')); ?>

                            </span>
                            <span class="hidden sm:inline-flex items-center gap-1">
                                <iconify-icon icon="solar:ticket-linear" width="13" class="text-slate-400"></iconify-icon>
                                <?php echo e($org->tickets_count); ?> <?php echo e(__('super_admin.organizations.col_tickets')); ?>

                            </span>
                            <span class="hidden md:inline-flex items-center gap-1">
                                <iconify-icon icon="solar:calendar-linear" width="13" class="text-slate-400"></iconify-icon>
                                <?php echo e($org->created_at?->format('d/m/Y')); ?>

                            </span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($org->creator): ?>
                                <span class="hidden lg:inline-flex items-center gap-1">
                                    <iconify-icon icon="solar:user-linear" width="13" class="text-slate-400"></iconify-icon>
                                    <?php echo e($org->creator->name); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="hidden xl:flex items-center gap-2">
                        <div class="flex items-center gap-1.5 rounded-lg bg-slate-50 border border-slate-100 px-2.5 py-1.5">
                            <iconify-icon icon="solar:users-group-rounded-bold-duotone" width="14" class="text-slate-400"></iconify-icon>
                            <span class="text-xs font-semibold text-slate-700 tabular-nums"><?php echo e($org->memberships_count); ?></span>
                        </div>
                        <div class="flex items-center gap-1.5 rounded-lg bg-slate-50 border border-slate-100 px-2.5 py-1.5">
                            <iconify-icon icon="solar:ticket-bold-duotone" width="14" class="text-slate-400"></iconify-icon>
                            <span class="text-xs font-semibold text-slate-700 tabular-nums"><?php echo e($org->tickets_count); ?></span>
                        </div>
                    </div>

                    
                    <div class="flex items-center gap-1.5 shrink-0">
                        <?php if(auth()->user()->canPlatformManage() && ($status === 'active')): ?>
                            <button wire:click="openEnterModal(<?php echo e($org->id); ?>)" class="inline-flex items-center gap-1.5 rounded-xl bg-[#005F02]/5 px-3 py-2 text-xs font-semibold text-[#005F02] hover:bg-[#005F02]/10 transition-colors" title="<?php echo e(__('super_admin.organizations.enter')); ?>">
                                <iconify-icon icon="solar:login-bold" width="14"></iconify-icon>
                                <span class="hidden lg:inline"><?php echo e(__('super_admin.organizations.enter')); ?></span>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <a href="<?php echo e(route('platform-admin.organizations.show', $org)); ?>" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors" title="<?php echo e(__('super_admin.organizations.view')); ?>">
                            <iconify-icon icon="solar:eye-bold" width="16"></iconify-icon>
                        </a>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canPlatformManage()): ?>
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                                    <iconify-icon icon="solar:menu-dots-bold" width="16"></iconify-icon>
                                </button>

                                <div
                                    x-show="open" @click.away="open = false"
                                    x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute right-0 z-50 mt-1 w-48 rounded-xl bg-white shadow-xl ring-1 ring-slate-200/60 py-1"
                                    style="display: none;"
                                >
                                    <a href="<?php echo e(route('platform-admin.support-sessions', ['org' => $org->id])); ?>" class="flex w-full items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                        <iconify-icon icon="solar:headphones-round-bold-duotone" width="15" class="text-violet-500"></iconify-icon>
                                        <?php echo e(__('super_admin.organizations.support')); ?>

                                    </a>

                                    <div class="my-1 border-t border-slate-100"></div>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($status !== 'active'): ?>
                                        <button @click="open = false" wire:click="openActivateModal(<?php echo e($org->id); ?>)" class="flex w-full items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-emerald-700 hover:bg-emerald-50 transition-colors">
                                            <iconify-icon icon="solar:check-circle-bold-duotone" width="15" class="text-emerald-500"></iconify-icon>
                                            <?php echo e(__('super_admin.organizations.activate')); ?>

                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($status !== 'suspended'): ?>
                                        <button @click="open = false" wire:click="openSuspendModal(<?php echo e($org->id); ?>)" class="flex w-full items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-amber-700 hover:bg-amber-50 transition-colors">
                                            <iconify-icon icon="solar:pause-circle-bold-duotone" width="15" class="text-amber-500"></iconify-icon>
                                            <?php echo e(__('super_admin.organizations.suspend')); ?>

                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $org->isArchived()): ?>
                                        <button @click="open = false" wire:click="openArchiveModal(<?php echo e($org->id); ?>)" class="flex w-full items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                            <iconify-icon icon="solar:archive-bold-duotone" width="15" class="text-slate-400"></iconify-icon>
                                            <?php echo e(__('super_admin.organizations.archive')); ?>

                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($status !== 'disabled'): ?>
                                        <div class="my-1 border-t border-slate-100"></div>
                                        <button @click="open = false" wire:click="openDisableModal(<?php echo e($org->id); ?>)" class="flex w-full items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                                            <iconify-icon icon="solar:close-circle-bold-duotone" width="15" class="text-red-500"></iconify-icon>
                                            <?php echo e(__('super_admin.organizations.disable')); ?>

                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_5): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-16">
                <div class="flex flex-col items-center justify-center text-slate-400">
                    <iconify-icon icon="solar:buildings-bold-duotone" width="44" class="mb-3 text-slate-300"></iconify-icon>
                    <p class="text-sm font-medium"><?php echo e(__('super_admin.organizations.empty')); ?></p>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($organizations->hasPages()): ?>
        <div class="flex justify-center">
            <?php echo e($organizations->links()); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Create Modal -->
    <div x-data="{ open: $wire.$entangle('showCreateModal') }" x-show="open" x-cloak @keydown.escape.window="open && (open = false)" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" @click.stop>
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F2E3BB]/30">
                    <iconify-icon icon="solar:buildings-bold-duotone" width="20" class="text-[#005F02]"></iconify-icon>
                </div>
                <h3 class="text-lg font-semibold text-slate-800"><?php echo e(__('super_admin.organizations.create_title')); ?></h3>
            </div>
            <div class="space-y-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1"><?php echo e(__('super_admin.organizations.create_name')); ?></label>
                    <input type="text" wire:model.live.debounce.300ms="createName" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all" />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['createName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1"><?php echo e(__('super_admin.organizations.create_slug')); ?></label>
                    <input type="text" wire:model="createSlug" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all" />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['createSlug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1"><?php echo e(__('super_admin.organizations.create_owner_email')); ?></label>
                    <input type="email" wire:model="createOwnerEmail" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all" />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['createOwnerEmail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button @click="open = false" class="sa-btn-secondary"><?php echo e(__('super_admin.cancel')); ?></button>
                <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['wire:click' => 'confirmCreate','wireTarget' => 'confirmCreate','variant' => 'super']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'confirmCreate','wire-target' => 'confirmCreate','variant' => 'super']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php echo e(__('super_admin.organizations.create')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $attributes = $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $component = $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Archive Modal -->
    <div x-data="{ open: $wire.$entangle('showArchiveModal') }" x-show="open" x-cloak @keydown.escape.window="open && (open = false)" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" @click.stop>
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100">
                    <iconify-icon icon="solar:archive-bold-duotone" width="20" class="text-slate-600"></iconify-icon>
                </div>
                <h3 class="text-lg font-semibold text-slate-800"><?php echo e(__('super_admin.organizations.archive_title')); ?></h3>
            </div>
            <p class="text-sm text-slate-500 mb-4"><?php echo e(__('super_admin.organizations.archive_desc')); ?></p>
            <div class="flex justify-end gap-2">
                <button @click="open = false" class="sa-btn-secondary"><?php echo e(__('super_admin.cancel')); ?></button>
                <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['wire:click' => 'confirmArchive','wireTarget' => 'confirmArchive','variant' => 'neutral']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'confirmArchive','wire-target' => 'confirmArchive','variant' => 'neutral']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php echo e(__('super_admin.organizations.confirm_archive')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $attributes = $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $component = $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Suspend Modal -->
    <div x-data="{ open: $wire.$entangle('showSuspendModal') }" x-show="open" x-cloak @keydown.escape.window="open && (open = false)" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" @click.stop>
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50">
                    <iconify-icon icon="solar:pause-circle-bold-duotone" width="20" class="text-amber-600"></iconify-icon>
                </div>
                <h3 class="text-lg font-semibold text-slate-800"><?php echo e(__('super_admin.organizations.suspend_modal_title')); ?></h3>
            </div>
            <p class="text-sm text-slate-500 mb-4"><?php echo e(__('super_admin.organizations.suspend_modal_desc')); ?></p>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1"><?php echo e(__('super_admin.organizations.suspend_reason')); ?></label>
                <textarea wire:model="suspendReason" rows="3" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all" placeholder="<?php echo e(__('super_admin.organizations.suspend_reason_placeholder')); ?>"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button @click="open = false" class="sa-btn-secondary"><?php echo e(__('super_admin.cancel')); ?></button>
                <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['wire:click' => 'confirmSuspend','wireTarget' => 'confirmSuspend','variant' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'confirmSuspend','wire-target' => 'confirmSuspend','variant' => 'warning']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php echo e(__('super_admin.organizations.confirm_suspend')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $attributes = $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $component = $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Enter organization modal -->
    <div x-data="{ open: $wire.$entangle('showEnterModal') }" x-show="open" x-cloak @keydown.escape.window="open && (open = false)" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" @click.stop>
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F2E3BB]/30">
                    <iconify-icon icon="solar:login-bold-duotone" width="20" class="text-[#005F02]"></iconify-icon>
                </div>
                <h3 class="text-lg font-semibold text-slate-800"><?php echo e(__('super_admin.organizations.enter')); ?></h3>
            </div>
            <p class="text-sm text-slate-500 mb-4"><?php echo e(__('super_admin.organizations.confirm_enter')); ?></p>
            <div class="flex justify-end gap-2">
                <button @click="open = false" class="sa-btn-secondary"><?php echo e(__('super_admin.cancel')); ?></button>
                <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['wire:click' => 'confirmEnter','wireTarget' => 'confirmEnter','variant' => 'super']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'confirmEnter','wire-target' => 'confirmEnter','variant' => 'super']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php echo e(__('super_admin.organizations.enter')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $attributes = $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $component = $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Activate organization modal -->
    <div x-data="{ open: $wire.$entangle('showActivateModal') }" x-show="open" x-cloak @keydown.escape.window="open && (open = false)" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" @click.stop>
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50">
                    <iconify-icon icon="solar:check-circle-bold-duotone" width="20" class="text-emerald-600"></iconify-icon>
                </div>
                <h3 class="text-lg font-semibold text-slate-800"><?php echo e(__('super_admin.organizations.activate')); ?></h3>
            </div>
            <p class="text-sm text-slate-500 mb-4"><?php echo e(__('super_admin.organizations.confirm_activate')); ?></p>
            <div class="flex justify-end gap-2">
                <button @click="open = false" class="sa-btn-secondary"><?php echo e(__('super_admin.cancel')); ?></button>
                <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['wire:click' => 'confirmActivate','wireTarget' => 'confirmActivate','variant' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'confirmActivate','wire-target' => 'confirmActivate','variant' => 'success']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php echo e(__('super_admin.organizations.activate')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $attributes = $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $component = $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Disable organization modal -->
    <div x-data="{ open: $wire.$entangle('showDisableModal') }" x-show="open" x-cloak @keydown.escape.window="open && (open = false)" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" @click.stop>
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50">
                    <iconify-icon icon="solar:close-circle-bold-duotone" width="20" class="text-red-600"></iconify-icon>
                </div>
                <h3 class="text-lg font-semibold text-slate-800"><?php echo e(__('super_admin.organizations.disable')); ?></h3>
            </div>
            <p class="text-sm text-slate-500 mb-4"><?php echo e(__('super_admin.organizations.confirm_disable')); ?></p>
            <div class="flex justify-end gap-2">
                <button @click="open = false" class="sa-btn-secondary"><?php echo e(__('super_admin.cancel')); ?></button>
                <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['wire:click' => 'confirmDisable','wireTarget' => 'confirmDisable','variant' => 'danger-solid']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'confirmDisable','wire-target' => 'confirmDisable','variant' => 'danger-solid']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php echo e(__('super_admin.organizations.disable')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $attributes = $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7)): ?>
<?php $component = $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7; ?>
<?php unset($__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7); ?>
<?php endif; ?>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\super-admin\organizations.blade.php ENDPATH**/ ?>