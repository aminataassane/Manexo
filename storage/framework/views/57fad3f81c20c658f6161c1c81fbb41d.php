<div class="w-full max-w-full min-w-0 mx-auto" wire:init="loadPage" x-data="{ tab: $wire.entangle('activeTab').live }">
    
    <div class="page-header">
        <div class="min-w-0">
            <h1 class="page-title"><?php echo e(__('settings.title')); ?></h1>
            <p class="page-subtitle"><?php echo e(__('settings.subtitle')); ?></p>
        </div>
        <div class="page-actions">
            <a
                href="<?php echo e(route('dashboard')); ?>"
                wire:navigate
                class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all touch-target sm:min-h-0 sm:min-w-0"
                style="border: 1px solid #e2e8f0;"
            >
                <iconify-icon icon="solar:arrow-left-linear" width="18"></iconify-icon>
                <?php echo e(__('settings.back')); ?>

            </a>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('settings_status') || $successMessage): ?>
        <div class="mb-6 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm flex items-center gap-3" style="border: 1px solid #a7f3d0;">
            <iconify-icon icon="solar:check-circle-bold" width="22"></iconify-icon>
            <span><?php echo e($successMessage ?: session('settings_status')); ?></span>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="mb-6 rounded-xl bg-red-50 p-4 text-sm text-red-800 shadow-sm" style="border: 1px solid #fecaca;">
            <p class="font-semibold flex items-center gap-2 mb-2">
                <iconify-icon icon="solar:danger-triangle-bold" width="20"></iconify-icon>
                <?php echo e(__('settings.fix_errors')); ?>

            </p>
            <ul class="list-disc list-inside space-y-1">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <li><?php echo e($err); ?></li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php
        $navItems = [
            ['key' => 'branding', 'icon' => 'solar:palette-bold-duotone', 'label' => __('settings.appearance')],
            ['key' => 'tickets', 'icon' => 'solar:ticket-bold-duotone', 'label' => __('menu.tickets')],
            ['key' => 'categories', 'icon' => 'solar:tag-bold-duotone', 'label' => __('settings.categories')],
            ['key' => 'groups', 'icon' => 'solar:widget-5-bold-duotone', 'label' => __('settings.groups')],
            ['key' => 'priorities', 'icon' => 'solar:flag-bold-duotone', 'label' => __('settings.priorities')],
            ['key' => 'functions', 'icon' => 'solar:user-id-bold-duotone', 'label' => __('settings.business_functions')],
            ['key' => 'forms', 'icon' => 'solar:clipboard-list-bold-duotone', 'label' => __('settings.forms')],
            ['key' => 'email', 'icon' => 'solar:letter-bold-duotone', 'label' => 'Email'],
            ['key' => 'sla', 'icon' => 'solar:alarm-bold-duotone', 'label' => 'SLA'],
            ['key' => 'automations', 'icon' => 'solar:bolt-circle-bold-duotone', 'label' => 'Automatisations'],
            ['key' => 'knowledge_base', 'icon' => 'solar:book-2-bold-duotone', 'label' => 'Base de connaissances'],
            ['key' => 'api', 'icon' => 'solar:programming-bold-duotone', 'label' => 'API'],
            ['key' => 'roles', 'icon' => 'solar:shield-keyhole-bold-duotone', 'label' => __('settings.roles_permissions')],
            ['key' => 'maintenance', 'icon' => 'solar:tuning-2-bold-duotone', 'label' => __('settings.maintenance')],
            ['key' => 'danger', 'icon' => 'solar:danger-triangle-bold-duotone', 'label' => __('settings.danger_zone')],
        ];
    ?>

    
    <div class="lg:hidden mb-4 -mx-1">
        <div
            class="overflow-x-auto scrollbar-hide overscroll-x-contain [touch-action:pan-x]"
            x-ref="mobileNav"
        >
            <div class="flex gap-1.5 px-1 pb-1 min-w-max">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nav): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <button type="button"
                        wire:click="setActiveTab('<?php echo e($nav['key']); ?>')"
                        @click="tab = '<?php echo e($nav['key']); ?>'; $nextTick(() => $el.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' }))"
                        class="shrink-0 inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-sm font-medium whitespace-nowrap transition-all"
                        :class="tab === '<?php echo e($nav['key']); ?>'
                            ? '<?php echo e($nav['key'] === 'danger' ? 'bg-red-50 text-red-700 shadow-sm' : 'bg-[var(--accent)]/10 text-[var(--accent)] shadow-sm'); ?>'
                            : '<?php echo e($nav['key'] === 'danger' ? 'text-slate-500 hover:bg-red-50 hover:text-red-700' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700'); ?>'"
                    >
                        <iconify-icon icon="<?php echo e($nav['icon']); ?>" width="16" class="shrink-0"
                            :class="tab === '<?php echo e($nav['key']); ?>'
                                ? '<?php echo e($nav['key'] === 'danger' ? 'text-red-600' : 'text-[var(--accent)]'); ?>'
                                : 'text-slate-400'"
                        ></iconify-icon>
                        <?php echo e($nav['label']); ?>

                    </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <div class="hidden lg:block lg:col-span-3 space-y-4 sm:space-y-6">
            <div class="sidebar-panel sticky top-24">
                <div class="sidebar-panel-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nav): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($nav['key'] === 'danger'): ?>
                            <div class="pt-3 mt-3" style="border-top: 1px solid #f1f5f9;">
                                <button type="button" wire:click="setActiveTab('danger')" @click="tab = 'danger'"
                                    class="sidebar-item" :class="tab === 'danger' ? 'bg-red-50 text-red-700' : 'text-slate-600 hover:bg-red-50 hover:text-red-700'">
                                    <span class="flex items-center gap-2.5">
                                        <iconify-icon icon="solar:danger-triangle-bold-duotone" width="18" :class="tab === 'danger' ? 'text-red-600' : 'text-slate-400'" class="shrink-0"></iconify-icon>
                                        <?php echo e(__('settings.danger_zone')); ?>

                                    </span>
                                </button>
                            </div>
                        <?php else: ?>
                            <button type="button" wire:click="setActiveTab('<?php echo e($nav['key']); ?>')" @click="tab = '<?php echo e($nav['key']); ?>'"
                                class="sidebar-item" :class="tab === '<?php echo e($nav['key']); ?>' ? 'sidebar-item-active' : 'sidebar-item-default'">
                                <span class="flex items-center gap-2.5">
                                    <iconify-icon icon="<?php echo e($nav['icon']); ?>" width="18" :class="tab === '<?php echo e($nav['key']); ?>' ? 'text-[var(--accent)]' : 'text-slate-400'" class="shrink-0"></iconify-icon>
                                    <?php echo e($nav['label']); ?>

                                </span>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
                <div wire:loading.delay.400ms.flex wire:target="setActiveTab" class="items-center justify-center gap-2 px-3 py-2 text-xs text-slate-500">
                    <iconify-icon icon="solar:refresh-linear" class="animate-spin" width="14"></iconify-icon>
                    <?php echo e(__('settings.loading_tab')); ?>

                </div>
            </div>

            
            <div class="rounded-2xl bg-slate-50 p-5" style="border: 1px solid #f1f5f9;">
                <div class="flex items-start gap-3">
                    <iconify-icon icon="solar:info-circle-bold" class="text-slate-400 mt-0.5 shrink-0" width="20"></iconify-icon>
                    <div>
                        <h4 class="text-sm font-semibold text-slate-900"><?php echo e(__('settings.need_help')); ?></h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            <?php echo e(__('settings.help_text')); ?>

                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT CONTENT AREA -->
        <div class="lg:col-span-9 space-y-6 <?php if(! $ready): ?> animate-pulse <?php endif; ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $canManage): ?>
                <div class="rounded-xl bg-amber-50 p-4 text-sm text-amber-800 flex items-center gap-3" style="border: 1px solid #fde68a;">
                    <iconify-icon icon="solar:lock-keyhole-bold" width="20" class="shrink-0"></iconify-icon>
                    <?php echo e(__('settings.read_only')); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- BRANDING TAB -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'branding'): ?>
            <div class="content-card">
                <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                    <h2 class="text-lg font-bold text-slate-900"><?php echo e(__('settings.branding_title')); ?></h2>
                    <p class="text-sm text-slate-500"><?php echo e(__('settings.branding_subtitle')); ?></p>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-8">
                    <!-- Logo Section -->
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-4"><?php echo e(__('settings.company_logo')); ?></h3>
                        <div class="flex items-start gap-6">
                            <div class="shrink-0 relative group">
                                <div class="h-24 w-24 rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 flex items-center justify-center overflow-hidden transition-colors hover:border-[var(--accent)] hover:bg-[var(--accent-soft)]/10">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($logo): ?>
                                        <img src="<?php echo e($logo->temporaryUrl()); ?>" class="h-full w-full object-cover" alt="Preview">
                                    <?php elseif($currentLogoUrl): ?>
                                        <img src="<?php echo e($currentLogoUrl); ?>" class="h-full w-full object-cover" alt="Logo">
                                    <?php else: ?>
                                        <iconify-icon icon="solar:gallery-add-linear" class="text-slate-400 group-hover:text-[var(--accent)]" width="32"></iconify-icon>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currentLogoUrl || $logo): ?>
                                    <button type="button" @click="$dispatch('confirm-action', { title: '<?php echo e(__('Supprimer')); ?>', message: '<?php echo e(__('Supprimer le logo de l\u0027organisation ?')); ?>', confirmLabel: '<?php echo e(__('Supprimer')); ?>', variant: 'danger', onConfirm: () => $wire.removeLogo() })" class="absolute -top-2 -right-2 h-6 w-6 rounded-full bg-red-100 text-red-600 flex items-center justify-center hover:bg-red-200 transition-colors shadow-sm" title="<?php echo e(__('settings.remove')); ?>">
                                        <iconify-icon icon="solar:trash-bin-trash-bold" width="14"></iconify-icon>
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="flex-1 space-y-3">
                                <div class="flex items-center gap-3">
                                    <label for="logo-upload" class="cursor-pointer inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                                        <iconify-icon icon="solar:upload-linear" width="18"></iconify-icon>
                                        <?php echo e(__('settings.upload_logo')); ?>

                                        <input id="logo-upload" type="file" class="hidden" accept="image/*" wire:model="logo" <?php if(! $canManage): echo 'disabled'; endif; ?>>
                                    </label>
                                    <span class="text-xs text-slate-500"><?php echo e(__('settings.logo_formats')); ?></span>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    <?php echo e(__('settings.logo_usage')); ?>

                                </p>
                                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('logo')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('logo'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    <!-- General Info -->
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'org_name','value' => 'Nom de l’entreprise']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'org_name','value' => 'Nom de l’entreprise']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                            <input
                                id="org_name"
                                type="text"
                                wire:model.blur="name"
                                required
                                <?php if(! $canManage): echo 'disabled'; endif; ?>
                                class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all disabled:bg-slate-50 disabled:text-slate-500"
                            />
                            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('name'),'class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('name')),'class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                        </div>
                        <div>
                            <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'org_slug','value' => __('settings.space_slug')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'org_slug','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.space_slug'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                            <div class="mt-1 flex rounded-xl shadow-sm">
                                <span class="inline-flex items-center rounded-l-xl border border-r-0 border-slate-200 bg-slate-50 px-3 text-sm text-slate-500">manexo.io/</span>
                                <input type="text" id="org_slug" wire:model.blur="slug" class="block w-full min-w-0 flex-1 rounded-none rounded-r-xl border-slate-200 focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm" <?php if(! $canManage): echo 'disabled'; endif; ?>>
                            </div>
                            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('slug'),'class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('slug')),'class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                        </div>
                    </div>

                    <!-- Colors -->
                    <div>
                        <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'primary_color','value' => __('settings.primary_color')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'primary_color','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.primary_color'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                        <div class="mt-2 flex items-center gap-4">
                            <div class="relative group cursor-pointer">
                                <input type="color" id="color-picker" class="absolute inset-0 h-full w-full opacity-0 cursor-pointer z-10" wire:model.debounce.300ms="primary_color" <?php if(! $canManage): echo 'disabled'; endif; ?>>
                                <div class="h-11 w-11 rounded-xl shadow-sm border border-slate-200 flex items-center justify-center transition-transform group-hover:scale-105" style="background-color: <?php echo e($primary_color ?? '#005F02'); ?>;">
                                    <iconify-icon icon="solar:pen-new-square-linear" class="text-white opacity-50"></iconify-icon>
                                </div>
                            </div>
                            <div class="flex-1 max-w-xs">
                                <input
                                    id="primary_color"
                                    type="text"
                                    class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm font-mono uppercase focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all disabled:bg-slate-50 disabled:text-slate-500"
                                    wire:model.debounce.300ms="primary_color"
                                    maxlength="7"
                                    <?php if(! $canManage): echo 'disabled'; endif; ?>
                                    placeholder="#000000"
                                />
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-slate-500"><?php echo e(__('settings.primary_color_help')); ?></p>
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('primary_color'),'class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('primary_color')),'class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','wireTarget' => 'save','variant' => 'primary','class' => 'btn-primary','loadingLabel' => __('ui.action.saving'),'disabled' => ! $canManage]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire-target' => 'save','variant' => 'primary','class' => 'btn-primary','loading-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.action.saving')),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(! $canManage)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php echo e(__('settings.save_changes')); ?>

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
                    </div>
                </form>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- TICKETS TAB -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'tickets'): ?>
            <div class="content-card">
                <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                    <h2 class="text-lg font-bold text-slate-900"><?php echo e(__('settings.tickets_title')); ?></h2>
                    <p class="text-sm text-slate-500"><?php echo e(__('settings.tickets_subtitle')); ?></p>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-8">
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'default_category','value' => __('settings.default_category')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'default_category','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.default_category'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                            <div class="mt-1">
                                <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['id' => 'default_category','wire:model' => 'default_category_id','disabled' => ! $canManage]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'default_category','wire:model' => 'default_category_id','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(! $canManage)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                    <option value=""><?php echo e(__('settings.select')); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option>
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
                            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('default_category_id'),'class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('default_category_id')),'class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                        </div>

                        <div>
                            <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'default_priority','value' => __('settings.default_priority')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'default_priority','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.default_priority'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                            <div class="mt-1">
                                <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['id' => 'default_priority','wire:model' => 'default_priority_id','disabled' => ! $canManage]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'default_priority','wire:model' => 'default_priority_id','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(! $canManage)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                    <option value=""><?php echo e(__('settings.select')); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option>
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
                            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('default_priority_id'),'class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('default_priority_id')),'class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-4"><?php echo e(__('settings.rules_title')); ?></h3>
                        <div class="space-y-4">
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors cursor-pointer">
                                <input type="checkbox" wire:model="members_can_edit" class="mt-1 rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]" <?php if(! $canManage): echo 'disabled'; endif; ?>>
                                <div>
                                    <span class="block text-sm font-medium text-slate-900"><?php echo e(__('settings.allow_edit')); ?></span>
                                    <span class="block text-xs text-slate-500 mt-0.5"><?php echo e(__('settings.allow_edit_help')); ?></span>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors cursor-pointer">
                                <input type="checkbox" wire:model="members_can_delete" class="mt-1 rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]" <?php if(! $canManage): echo 'disabled'; endif; ?>>
                                <div>
                                    <span class="block text-sm font-medium text-slate-900"><?php echo e(__('settings.allow_delete')); ?></span>
                                    <span class="block text-xs text-slate-500 mt-0.5"><?php echo e(__('settings.allow_delete_help')); ?></span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-4"><?php echo e(__('settings.ticket_resolution_mode_label')); ?></h3>
                        <div class="max-w-md">
                            <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['id' => 'ticket_resolution_mode','wire:model' => 'ticket_resolution_mode','disabled' => ! $canManage]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'ticket_resolution_mode','wire:model' => 'ticket_resolution_mode','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(! $canManage)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <option value="flexible"><?php echo e(__('settings.ticket_resolution_mode_flexible')); ?></option>
                                <option value="strict"><?php echo e(__('settings.ticket_resolution_mode_strict')); ?></option>
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

                    <hr class="border-slate-100">

                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-4"><?php echo e(__('settings.auto_close_title')); ?></h3>
                        <div class="max-w-xs">
                            <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'auto_close_days','value' => __('settings.auto_close_label')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'auto_close_days','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.auto_close_label'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                            <input
                                id="auto_close_days"
                                type="number"
                                wire:model="auto_close_days"
                                min="1"
                                max="365"
                                placeholder="<?php echo e(__('settings.auto_close_placeholder')); ?>"
                                class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all disabled:bg-slate-50 disabled:text-slate-500"
                                <?php if(! $canManage): echo 'disabled'; endif; ?>
                            />
                            <p class="text-xs text-slate-500 mt-1.5"><?php echo e(__('settings.auto_close_help')); ?></p>
                            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('auto_close_days'),'class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('auto_close_days')),'class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','wireTarget' => 'save','variant' => 'primary','class' => 'btn-primary','loadingLabel' => __('ui.action.saving'),'disabled' => ! $canManage]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire-target' => 'save','variant' => 'primary','class' => 'btn-primary','loading-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.action.saving')),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(! $canManage)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php echo e(__('settings.save')); ?>

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
                    </div>
                </form>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- CATEGORIES TAB -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'categories'): ?>
            <div class="content-card">
                <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                    <h2 class="text-lg font-bold text-slate-900"><?php echo e(__('settings.categories_title')); ?></h2>
                    <p class="text-sm text-slate-500"><?php echo e(__('settings.categories_subtitle')); ?></p>
                </div>

                <div class="p-6 space-y-6">
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
                        <form wire:submit.prevent="createCategory" class="space-y-3">
                            <div class="flex items-end gap-3">
                                <div class="flex-1">
                                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'new_cat_name','value' => __('settings.category_name')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'new_cat_name','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.category_name'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                                    <input
                                        id="new_cat_name"
                                        type="text"
                                        wire:model.blur="newCategoryName"
                                        placeholder="<?php echo e(__('settings.category_placeholder')); ?>"
                                        class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                                    />
                                    <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('newCategoryName'),'class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('newCategoryName')),'class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                                </div>
                                <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','wireTarget' => 'createCategory','variant' => 'primary','class' => 'shrink-0 !font-semibold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire-target' => 'createCategory','variant' => 'primary','class' => 'shrink-0 !font-semibold']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                    <iconify-icon icon="solar:add-circle-linear" width="18"></iconify-icon>
                                    <?php echo e(__('settings.add')); ?>

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
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['value' => __('settings.default_group')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.default_group'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                                    <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['wire:model' => 'newCategoryDefaultGroupId','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'newCategoryDefaultGroupId','class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                        <option value=""><?php echo e(__('settings.none')); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $ticketGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                            <option value="<?php echo e($tg->id); ?>"><?php echo e($tg->name); ?></option>
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
                                <div>
                                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['value' => __('settings.default_form')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.default_form'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                                    <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['wire:model' => 'newCategoryDefaultFormId','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'newCategoryDefaultFormId','class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                        <option value=""><?php echo e(__('settings.none')); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $forms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                            <option value="<?php echo e($f->id); ?>"><?php echo e($f->name); ?></option>
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
                            
                            <div class="mt-3 rounded-xl border border-slate-100 bg-slate-50/50 p-3 space-y-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="newCategoryRequiresApproval" class="rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]" />
                                    <span class="text-sm font-medium text-slate-700">Nécessite une approbation</span>
                                </label>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($newCategoryRequiresApproval): ?>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['value' => 'Type d\'approbateur']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 'Type d\'approbateur']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                                            <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['wire:model' => 'newCategoryApprovalType','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'newCategoryApprovalType','class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                <option value="">Choisir...</option>
                                                <option value="user">Utilisateur spécifique</option>
                                                <option value="role">Rôle</option>
                                                <option value="org_admin">Owner / Admin de l'org</option>
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
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($newCategoryApprovalType === 'user'): ?>
                                            <div>
                                                <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['value' => 'Approbateur']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 'Approbateur']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                                                <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['wire:model' => 'newCategoryApprovalUserId','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'newCategoryApprovalUserId','class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                    <option value="">Choisir...</option>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                        <option value="<?php echo e($m->user?->id); ?>"><?php echo e($m->user?->name); ?> (<?php echo e($m->role); ?>)</option>
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
                                        <?php elseif($newCategoryApprovalType === 'role'): ?>
                                            <div>
                                                <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['value' => 'Rôle approbateur']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 'Rôle approbateur']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                                                <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['wire:model' => 'newCategoryApprovalRole','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'newCategoryApprovalRole','class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                    <option value="">Choisir...</option>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                        <option value="<?php echo e($r->slug); ?>"><?php echo e($r->label ?? $r->slug); ?></option>
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
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </form>
                        <hr class="border-slate-100">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categories->isEmpty()): ?>
                        <div class="py-8 text-center">
                            <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 mb-3">
                                <iconify-icon icon="solar:tag-linear" width="28" class="text-slate-400"></iconify-icon>
                            </div>
                            <p class="text-sm text-slate-500"><?php echo e(__('settings.no_categories')); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors group"
                                     <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processElementKey('cat-{{ $cat->id }}', get_defined_vars()); ?>wire:key="cat-<?php echo e($cat->id); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editingCategoryId === $cat->id): ?>
                                        
                                        <form wire:submit.prevent="updateCategory" class="flex-1 space-y-2">
                                            <div class="flex items-center gap-3">
                                                <input
                                                    type="text"
                                                    wire:model.blur="editingCategoryName"
                                                    class="flex-1 rounded-lg border-slate-200 py-1.5 px-2.5 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                                    autofocus
                                                />
                                                <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','wireTarget' => 'updateCategory','variant' => 'ghost','iconOnly' => true,'class' => '!p-1 text-[var(--accent)] hover:opacity-80','title' => ''.e(__('settings.save')).'','loadingLabel' => __('settings.save')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire-target' => 'updateCategory','variant' => 'ghost','icon-only' => true,'class' => '!p-1 text-[var(--accent)] hover:opacity-80','title' => ''.e(__('settings.save')).'','loading-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.save'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                    <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
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
                                                <button type="button" wire:click="cancelEditCategory" class="text-slate-400 hover:text-slate-600" title="<?php echo e(__('settings.cancel')); ?>">
                                                    <iconify-icon icon="solar:close-circle-bold" width="20"></iconify-icon>
                                                </button>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2">
                                                <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['wire:model' => 'editingCategoryDefaultGroupId','class' => 'text-xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'editingCategoryDefaultGroupId','class' => 'text-xs']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                    <option value=""><?php echo e(__('settings.default_group')); ?>: <?php echo e(__('settings.none')); ?></option>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $ticketGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                        <option value="<?php echo e($tg->id); ?>"><?php echo e($tg->name); ?></option>
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
                                                <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['wire:model' => 'editingCategoryDefaultFormId','class' => 'text-xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'editingCategoryDefaultFormId','class' => 'text-xs']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                    <option value=""><?php echo e(__('settings.default_form')); ?>: <?php echo e(__('settings.none')); ?></option>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $forms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                        <option value="<?php echo e($f->id); ?>"><?php echo e($f->name); ?></option>
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
                                            
                                            <div class="mt-2 rounded-lg border border-slate-100 bg-slate-50/50 p-2.5 space-y-2">
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="checkbox" wire:model="editingCategoryRequiresApproval" class="rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]" />
                                                    <span class="text-xs font-medium text-slate-700">Nécessite une approbation</span>
                                                </label>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editingCategoryRequiresApproval): ?>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['wire:model' => 'editingCategoryApprovalType','class' => 'text-xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'editingCategoryApprovalType','class' => 'text-xs']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                            <option value="">Type...</option>
                                                            <option value="user">Utilisateur</option>
                                                            <option value="role">Rôle</option>
                                                            <option value="org_admin">Owner/Admin</option>
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
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editingCategoryApprovalType === 'user'): ?>
                                                            <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['wire:model' => 'editingCategoryApprovalUserId','class' => 'text-xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'editingCategoryApprovalUserId','class' => 'text-xs']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                                <option value="">Approbateur...</option>
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                                    <option value="<?php echo e($m->user?->id); ?>"><?php echo e($m->user?->name); ?></option>
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
                                                        <?php elseif($editingCategoryApprovalType === 'role'): ?>
                                                            <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['wire:model' => 'editingCategoryApprovalRole','class' => 'text-xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'editingCategoryApprovalRole','class' => 'text-xs']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                                <option value="">Rôle...</option>
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                                    <option value="<?php echo e($r->slug); ?>"><?php echo e($r->label ?? $r->slug); ?></option>
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
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </form>
                                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('editingCategoryName'),'class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('editingCategoryName')),'class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                                    <?php else: ?>
                                        
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-sm font-medium text-slate-900"><?php echo e($cat->name); ?></span>
                                                <span class="text-[10px] font-mono text-slate-400 bg-slate-50 px-1.5 py-0.5 rounded"><?php echo e($cat->slug); ?></span>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cat->default_ticket_group_id): ?>
                                                    <?php $linkedGroup = $ticketGroups->firstWhere('id', $cat->default_ticket_group_id); ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($linkedGroup): ?>
                                                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold bg-blue-50 text-blue-700">
                                                            <iconify-icon icon="solar:widget-5-linear" width="10"></iconify-icon>
                                                            <?php echo e($linkedGroup->name); ?>

                                                        </span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cat->default_form_id): ?>
                                                    <?php $linkedForm = $forms->firstWhere('id', $cat->default_form_id); ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($linkedForm): ?>
                                                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold bg-violet-50 text-violet-700">
                                                            <iconify-icon icon="solar:clipboard-list-linear" width="10"></iconify-icon>
                                                            <?php echo e($linkedForm->name); ?>

                                                        </span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cat->requires_approval): ?>
                                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold bg-amber-50 text-amber-700">
                                                        <iconify-icon icon="solar:shield-check-linear" width="10"></iconify-icon>
                                                        Approbation
                                                    </span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>

                                        
                                        <button
                                            type="button"
                                            wire:click="toggleCategory(<?php echo e($cat->id); ?>)"
                                            class="shrink-0 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold transition-colors <?php echo e($cat->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'); ?>"
                                            <?php if(! $canManage): echo 'disabled'; endif; ?>
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full <?php echo e($cat->is_active ? 'bg-emerald-500' : 'bg-slate-400'); ?>"></span>
                                            <?php echo e($cat->is_active ? __('settings.active') : __('settings.inactive')); ?>

                                        </button>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
                                            
                                            <button type="button" wire:click="startEditCategory(<?php echo e($cat->id); ?>)" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-[var(--accent)] transition-all" title="<?php echo e(__('settings.edit')); ?>">
                                                <iconify-icon icon="solar:pen-2-linear" width="16"></iconify-icon>
                                            </button>
                                            
                                            <button type="button" @click="$dispatch('confirm-action', { title: 'Supprimer', message: '<?php echo e(__('settings.delete_category_confirm')); ?>', confirmLabel: 'Supprimer', variant: 'danger', onConfirm: () => $wire.deleteCategory(<?php echo e($cat->id); ?>) })" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-red-600 transition-all" title="<?php echo e(__('settings.delete')); ?>">
                                                <iconify-icon icon="solar:trash-bin-trash-linear" width="16"></iconify-icon>
                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- GROUPS TAB -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'groups'): ?>
            <div class="content-card">
                <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                    <h2 class="text-lg font-bold text-slate-900"><?php echo e(__('settings.groups_title')); ?></h2>
                    <p class="text-sm text-slate-500"><?php echo e(__('settings.groups_subtitle')); ?></p>
                </div>

                <div class="p-6 space-y-6">
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
                        <form wire:submit.prevent="createGroup" class="flex items-end gap-3">
                            <div class="flex-1">
                                <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'new_group_name','value' => __('settings.group_name')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'new_group_name','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.group_name'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                                <input
                                    id="new_group_name"
                                    type="text"
                                    wire:model.blur="newGroupName"
                                    placeholder="<?php echo e(__('settings.group_placeholder')); ?>"
                                    class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                                />
                                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('newGroupName'),'class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('newGroupName')),'class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                            </div>
                            <div class="w-24">
                                <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'new_group_color','value' => __('settings.group_color')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'new_group_color','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.group_color'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                                <div class="mt-1 relative">
                                    <input type="color" id="new_group_color" wire:model="newGroupColor" class="h-[42px] w-full rounded-xl border border-slate-200 cursor-pointer" />
                                </div>
                            </div>
                            <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','wireTarget' => 'createGroup','variant' => 'primary','class' => 'shrink-0 !font-semibold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire-target' => 'createGroup','variant' => 'primary','class' => 'shrink-0 !font-semibold']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <iconify-icon icon="solar:add-circle-linear" width="18"></iconify-icon>
                                <?php echo e(__('settings.add')); ?>

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
                        </form>
                        <hr class="border-slate-100">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ticketGroups->isEmpty()): ?>
                        <div class="py-8 text-center">
                            <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 mb-3">
                                <iconify-icon icon="solar:widget-5-linear" width="28" class="text-slate-400"></iconify-icon>
                            </div>
                            <p class="text-sm text-slate-500"><?php echo e(__('settings.no_groups')); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $ticketGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors group"
                                     <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processElementKey('grp-{{ $grp->id }}', get_defined_vars()); ?>wire:key="grp-<?php echo e($grp->id); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editingGroupId === $grp->id): ?>
                                        
                                        <form wire:submit.prevent="updateGroup" class="flex-1 flex items-center gap-3">
                                            <input
                                                type="text"
                                                wire:model.blur="editingGroupName"
                                                class="flex-1 rounded-lg border-slate-200 py-1.5 px-2.5 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                                autofocus
                                            />
                                            <input
                                                type="color"
                                                wire:model="editingGroupColor"
                                                class="h-8 w-10 rounded border border-slate-200 cursor-pointer"
                                            />
                                            <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','wireTarget' => 'updateGroup','variant' => 'ghost','iconOnly' => true,'class' => '!p-1 text-[var(--accent)] hover:opacity-80','title' => ''.e(__('settings.save')).'','loadingLabel' => __('settings.save')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire-target' => 'updateGroup','variant' => 'ghost','icon-only' => true,'class' => '!p-1 text-[var(--accent)] hover:opacity-80','title' => ''.e(__('settings.save')).'','loading-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.save'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
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
                                            <button type="button" wire:click="cancelEditGroup" class="text-slate-400 hover:text-slate-600" title="<?php echo e(__('settings.cancel')); ?>">
                                                <iconify-icon icon="solar:close-circle-bold" width="20"></iconify-icon>
                                            </button>
                                        </form>
                                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('editingGroupName'),'class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('editingGroupName')),'class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                                    <?php else: ?>
                                        
                                        <span class="h-3 w-3 rounded-full shrink-0" style="background-color: <?php echo e($grp->color ?? 'var(--accent)'); ?>;"></span>

                                        
                                        <div class="flex-1 min-w-0">
                                            <span class="text-sm font-medium text-slate-900"><?php echo e($grp->name); ?></span>
                                            <span class="ml-2 text-[10px] font-mono text-slate-400 bg-slate-50 px-1.5 py-0.5 rounded"><?php echo e($grp->slug); ?></span>
                                        </div>

                                        
                                        <button
                                            type="button"
                                            wire:click="toggleGroup(<?php echo e($grp->id); ?>)"
                                            class="shrink-0 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold transition-colors <?php echo e($grp->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'); ?>"
                                            <?php if(! $canManage): echo 'disabled'; endif; ?>
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full <?php echo e($grp->is_active ? 'bg-emerald-500' : 'bg-slate-400'); ?>"></span>
                                            <?php echo e($grp->is_active ? __('settings.active') : __('settings.inactive')); ?>

                                        </button>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
                                            
                                            <button type="button" wire:click="startEditGroup(<?php echo e($grp->id); ?>)" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-[var(--accent)] transition-all" title="<?php echo e(__('settings.edit')); ?>">
                                                <iconify-icon icon="solar:pen-2-linear" width="16"></iconify-icon>
                                            </button>
                                            
                                            <button type="button" @click="$dispatch('confirm-action', { title: 'Supprimer', message: '<?php echo e(__('settings.delete_group_confirm')); ?>', confirmLabel: 'Supprimer', variant: 'danger', onConfirm: () => $wire.deleteGroup(<?php echo e($grp->id); ?>) })" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-red-600 transition-all" title="<?php echo e(__('settings.delete')); ?>">
                                                <iconify-icon icon="solar:trash-bin-trash-linear" width="16"></iconify-icon>
                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- PRIORITIES TAB -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'priorities'): ?>
            <div class="content-card">
                <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                    <h2 class="text-lg font-bold text-slate-900"><?php echo e(__('settings.priorities_title')); ?></h2>
                    <p class="text-sm text-slate-500"><?php echo e(__('settings.priorities_subtitle')); ?></p>
                </div>

                <div class="p-6 space-y-6">
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
                        <form wire:submit.prevent="createPriority" class="flex items-end gap-3">
                            <div class="flex-1">
                                <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'new_prio_name','value' => __('settings.name')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'new_prio_name','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.name'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                                <input
                                    id="new_prio_name"
                                    type="text"
                                    wire:model.blur="newPriorityName"
                                    placeholder="<?php echo e(__('settings.priority_placeholder')); ?>"
                                    class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                                />
                                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('newPriorityName'),'class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('newPriorityName')),'class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                            </div>
                            <div class="w-28">
                                <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'new_prio_level','value' => __('settings.level')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'new_prio_level','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.level'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                                <input
                                    id="new_prio_level"
                                    type="number"
                                    wire:model="newPriorityLevel"
                                    min="0"
                                    placeholder="0"
                                    class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                                />
                                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('newPriorityLevel'),'class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('newPriorityLevel')),'class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                            </div>
                            <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','wireTarget' => 'createPriority','variant' => 'primary','class' => 'shrink-0 !font-semibold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire-target' => 'createPriority','variant' => 'primary','class' => 'shrink-0 !font-semibold']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <iconify-icon icon="solar:add-circle-linear" width="18"></iconify-icon>
                                <?php echo e(__('settings.add')); ?>

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
                        </form>
                        <hr class="border-slate-100">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($priorities->isEmpty()): ?>
                        <div class="py-8 text-center">
                            <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 mb-3">
                                <iconify-icon icon="solar:flag-linear" width="28" class="text-slate-400"></iconify-icon>
                            </div>
                            <p class="text-sm text-slate-500"><?php echo e(__('settings.no_priorities')); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors group"
                                     <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processElementKey('prio-{{ $prio->id }}', get_defined_vars()); ?>wire:key="prio-<?php echo e($prio->id); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editingPriorityId === $prio->id): ?>
                                        
                                        <form wire:submit.prevent="updatePriority" class="flex-1 flex items-center gap-3">
                                            <input
                                                type="text"
                                                wire:model.blur="editingPriorityName"
                                                class="flex-1 rounded-lg border-slate-200 py-1.5 px-2.5 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                                autofocus
                                            />
                                            <input
                                                type="number"
                                                wire:model="editingPriorityLevel"
                                                min="0"
                                                class="w-20 rounded-lg border-slate-200 py-1.5 px-2.5 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                            />
                                            <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','wireTarget' => 'updatePriority','variant' => 'ghost','iconOnly' => true,'class' => '!p-1 text-[var(--accent)] hover:opacity-80','title' => ''.e(__('settings.save')).'','loadingLabel' => __('settings.save')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire-target' => 'updatePriority','variant' => 'ghost','icon-only' => true,'class' => '!p-1 text-[var(--accent)] hover:opacity-80','title' => ''.e(__('settings.save')).'','loading-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.save'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
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
                                            <button type="button" wire:click="cancelEditPriority" class="text-slate-400 hover:text-slate-600" title="<?php echo e(__('settings.cancel')); ?>">
                                                <iconify-icon icon="solar:close-circle-bold" width="20"></iconify-icon>
                                            </button>
                                        </form>
                                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('editingPriorityName'),'class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('editingPriorityName')),'class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('editingPriorityLevel'),'class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('editingPriorityLevel')),'class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                                    <?php else: ?>
                                        
                                        <div class="flex-1 min-w-0 flex items-center gap-3">
                                            <span class="text-sm font-medium text-slate-900"><?php echo e($prio->name); ?></span>
                                            <span class="inline-flex items-center justify-center h-6 min-w-[24px] rounded-md bg-slate-100 px-1.5 text-[11px] font-bold text-slate-600 tabular-nums"><?php echo e($prio->level); ?></span>
                                        </div>

                                        
                                        <button
                                            type="button"
                                            wire:click="togglePriority(<?php echo e($prio->id); ?>)"
                                            class="shrink-0 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold transition-colors <?php echo e($prio->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'); ?>"
                                            <?php if(! $canManage): echo 'disabled'; endif; ?>
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full <?php echo e($prio->is_active ? 'bg-emerald-500' : 'bg-slate-400'); ?>"></span>
                                            <?php echo e($prio->is_active ? __('Actif') : __('Inactif')); ?>

                                        </button>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
                                            
                                            <button type="button" wire:click="startEditPriority(<?php echo e($prio->id); ?>)" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-[var(--accent)] transition-all" title="<?php echo e(__('settings.edit')); ?>">
                                                <iconify-icon icon="solar:pen-2-linear" width="16"></iconify-icon>
                                            </button>
                                            
                                            <button type="button" @click="$dispatch('confirm-action', { title: 'Supprimer', message: '<?php echo e(__('settings.delete_priority_confirm')); ?>', confirmLabel: 'Supprimer', variant: 'danger', onConfirm: () => $wire.deletePriority(<?php echo e($prio->id); ?>) })" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-red-600 transition-all" title="<?php echo e(__('settings.delete')); ?>">
                                                <iconify-icon icon="solar:trash-bin-trash-linear" width="16"></iconify-icon>
                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- FONCTIONS MÉTIER TAB -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'functions'): ?>
            <div class="content-card">
                <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                    <h2 class="text-lg font-bold text-slate-900"><?php echo e(__('settings.functions_title')); ?></h2>
                    <p class="text-sm text-slate-500"><?php echo e(__('settings.functions_subtitle')); ?></p>
                </div>
                <div class="p-6 space-y-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
                        <form wire:submit.prevent="createFunction" class="flex items-end gap-3">
                            <div class="flex-1">
                                <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'new_function_name','value' => __('settings.function_name')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'new_function_name','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.function_name'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                                <input id="new_function_name" type="text" wire:model.blur="newFunctionName" placeholder="<?php echo e(__('settings.function_placeholder')); ?>" class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all" />
                                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('newFunctionName'),'class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('newFunctionName')),'class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                            </div>
                            <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','wireTarget' => 'createFunction','variant' => 'primary','class' => 'shrink-0 !font-semibold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire-target' => 'createFunction','variant' => 'primary','class' => 'shrink-0 !font-semibold']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <iconify-icon icon="solar:add-circle-linear" width="18"></iconify-icon>
                                <?php echo e(__('settings.add')); ?>

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
                        </form>
                        <hr class="border-slate-100">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($organizationFunctions->isEmpty()): ?>
                        <div class="py-8 text-center">
                            <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 mb-3">
                                <iconify-icon icon="solar:user-id-linear" width="28" class="text-slate-400"></iconify-icon>
                            </div>
                            <p class="text-sm text-slate-500"><?php echo e(__('settings.no_functions')); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $organizationFunctions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors group" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processElementKey('fn-{{ $fn->id }}', get_defined_vars()); ?>wire:key="fn-<?php echo e($fn->id); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editingFunctionId === $fn->id): ?>
                                        <form wire:submit.prevent="updateFunction" class="flex-1 flex items-center gap-3">
                                            <input type="text" wire:model.blur="editingFunctionName" class="flex-1 rounded-lg border-slate-200 py-1.5 px-2.5 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]" autofocus />
                                            <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','wireTarget' => 'updateFunction','variant' => 'ghost','iconOnly' => true,'class' => '!p-1 text-[var(--accent)] hover:opacity-80','title' => ''.e(__('settings.save')).'','loadingLabel' => __('settings.save')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire-target' => 'updateFunction','variant' => 'ghost','icon-only' => true,'class' => '!p-1 text-[var(--accent)] hover:opacity-80','title' => ''.e(__('settings.save')).'','loading-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.save'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
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
                                            <button type="button" wire:click="cancelEditFunction" class="text-slate-400 hover:text-slate-600" title="<?php echo e(__('settings.cancel')); ?>">
                                                <iconify-icon icon="solar:close-circle-bold" width="20"></iconify-icon>
                                            </button>
                                        </form>
                                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('editingFunctionName'),'class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('editingFunctionName')),'class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                                    <?php else: ?>
                                        <span class="flex-1 text-sm font-medium text-slate-900"><?php echo e($fn->name); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
                                            <button type="button" wire:click="startEditFunction(<?php echo e($fn->id); ?>)" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-[var(--accent)] transition-all" title="<?php echo e(__('settings.edit')); ?>">
                                                <iconify-icon icon="solar:pen-2-linear" width="16"></iconify-icon>
                                            </button>
                                            <button type="button" @click="$dispatch('confirm-action', { title: 'Supprimer', message: '<?php echo e(__('settings.delete_function_confirm')); ?>', confirmLabel: 'Supprimer', variant: 'danger', onConfirm: () => $wire.deleteFunction(<?php echo e($fn->id); ?>) })" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-red-600 transition-all" title="<?php echo e(__('settings.delete')); ?>">
                                                <iconify-icon icon="solar:trash-bin-trash-linear" width="16"></iconify-icon>
                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'forms'): ?>
            <div class="content-card">
                <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                    <h2 class="text-lg font-bold text-slate-900"><?php echo e(__('settings.forms_editor_title')); ?></h2>
                    <p class="text-sm text-slate-500 mt-1"><?php echo e(__('settings.forms_editor_subtitle')); ?></p>
                </div>
                <form wire:submit.prevent="saveFormsSettings" class="p-6 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="forms_default_due_days" class="block text-sm font-semibold text-slate-700 mb-1.5"><?php echo e(__('settings.forms_default_due_days')); ?></label>
                            <input type="number" id="forms_default_due_days" name="forms_default_due_days" wire:model.blur="forms_default_due_days" min="1" max="365" placeholder="7"
                                   class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm">
                            <p class="mt-1 text-xs text-slate-500"><?php echo e(__('settings.forms_default_due_days_help')); ?></p>
                        </div>
                        <div>
                            <label for="forms_default_expiry_days" class="block text-sm font-semibold text-slate-700 mb-1.5"><?php echo e(__('settings.forms_default_expiry_days')); ?></label>
                            <input type="number" id="forms_default_expiry_days" name="forms_default_expiry_days" wire:model.blur="forms_default_expiry_days" min="1" max="365" placeholder="30"
                                   class="block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm">
                            <p class="mt-1 text-xs text-slate-500"><?php echo e(__('settings.forms_default_expiry_days_help')); ?></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <input type="checkbox" id="forms_notify_on_response" name="forms_notify_on_response" wire:model="forms_notify_on_response"
                               class="h-4 w-4 rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]">
                        <div>
                            <label for="forms_notify_on_response" class="text-sm font-semibold text-slate-700"><?php echo e(__('settings.forms_notify_on_response')); ?></label>
                            <p class="text-xs text-slate-500 mt-0.5"><?php echo e(__('settings.forms_notify_on_response_help')); ?></p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','wireTarget' => 'saveFormsSettings','variant' => 'primary','class' => '!font-semibold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire-target' => 'saveFormsSettings','variant' => 'primary','class' => '!font-semibold']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <iconify-icon icon="solar:check-circle-bold" width="18"></iconify-icon>
                            <?php echo e(__('settings.save')); ?>

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
                        <a href="<?php echo e(route('admin.forms')); ?>" wire:navigate.hover class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                            <iconify-icon icon="solar:magic-stick-3-bold-duotone" width="18"></iconify-icon>
                            <?php echo e(__('settings.open_builder')); ?>

                        </a>
                    </div>
                </form>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'email'): ?>
                <?php echo $__env->make('livewire.admin.partials.settings-email', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'sla'): ?>
                <?php echo $__env->make('livewire.admin.partials.settings-sla', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'automations'): ?>
                <?php echo $__env->make('livewire.admin.partials.settings-automations', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'knowledge_base'): ?>
                <?php echo $__env->make('livewire.admin.partials.settings-knowledge-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'api'): ?>
                <?php echo $__env->make('livewire.admin.partials.settings-api', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- ROLES & PERMISSIONS TAB -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'roles'): ?>
            <div class="space-y-6">
                
                <div class="content-card">
                    <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                        <h2 class="text-lg font-bold text-slate-900"><?php echo e(__('settings.roles_management_title')); ?></h2>
                        <p class="text-sm text-slate-500"><?php echo e(__('settings.roles_management_subtitle')); ?></p>
                    </div>

                    <div class="p-6 space-y-6">
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
                            <form wire:submit.prevent="createRole" class="flex items-end gap-3">
                                <div class="flex-1">
                                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'new_role_name','value' => __('settings.role_name')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'new_role_name','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.role_name'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                                    <input
                                        id="new_role_name"
                                        type="text"
                                        wire:model.blur="newRoleName"
                                        placeholder="<?php echo e(__('settings.role_placeholder')); ?>"
                                        class="mt-1 block w-full rounded-xl border-slate-200 bg-white py-2.5 px-3 text-slate-900 shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] sm:text-sm transition-all"
                                    />
                                    <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('newRoleName'),'class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('newRoleName')),'class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                                </div>
                                <div class="w-44">
                                    <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'new_role_base','value' => __('settings.role_base')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'new_role_base','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.role_base'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                                    <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['id' => 'new_role_base','wire:model' => 'newRoleBaseSlug','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'new_role_base','wire:model' => 'newRoleBaseSlug','class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                        <option value=""><?php echo e(__('settings.role_base_none')); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->slug !== 'owner'): ?>
                                                <option value="<?php echo e($r->slug); ?>"><?php echo e($r->name); ?></option>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                                <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','wireTarget' => 'createRole','variant' => 'primary','class' => 'shrink-0 !font-semibold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire-target' => 'createRole','variant' => 'primary','class' => 'shrink-0 !font-semibold']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                    <iconify-icon icon="solar:add-circle-linear" width="18"></iconify-icon>
                                    <?php echo e(__('settings.add')); ?>

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
                            </form>
                            <hr class="border-slate-100">
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($roles->isEmpty()): ?>
                            <div class="py-8 text-center">
                                <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 mb-3">
                                    <iconify-icon icon="solar:shield-keyhole-linear" width="28" class="text-slate-400"></iconify-icon>
                                </div>
                                <p class="text-sm text-slate-500"><?php echo e(__('settings.no_roles')); ?></p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <div
                                        class="flex items-center gap-3 px-4 py-3 rounded-xl border transition-colors group cursor-pointer <?php echo e($selectedRole === $r->slug ? 'border-[var(--accent)] bg-[var(--accent-soft)]/10 ring-1 ring-[var(--accent)]/20' : 'border-slate-100 hover:border-slate-200'); ?>"
                                        <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processElementKey('role-{{ $r->id }}', get_defined_vars()); ?>wire:key="role-<?php echo e($r->id); ?>"
                                        wire:click="selectRoleTab('<?php echo e($r->slug); ?>')"
                                    >
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editingRoleId === $r->id): ?>
                                            
                                            <form wire:submit.prevent="updateRoleName" class="flex-1 flex items-center gap-3" @click.stop>
                                                <input
                                                    type="text"
                                                    wire:model.blur="editingRoleName"
                                                    class="flex-1 rounded-lg border-slate-200 py-1.5 px-2.5 text-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                                                    autofocus
                                                />
                                                <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','wireTarget' => 'updateRoleName','variant' => 'ghost','iconOnly' => true,'class' => '!p-1 text-[var(--accent)] hover:opacity-80','title' => ''.e(__('settings.save')).'','loadingLabel' => __('settings.save')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire-target' => 'updateRoleName','variant' => 'ghost','icon-only' => true,'class' => '!p-1 text-[var(--accent)] hover:opacity-80','title' => ''.e(__('settings.save')).'','loading-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.save'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                                    <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
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
                                                <button type="button" wire:click.stop="cancelEditRole" class="text-slate-400 hover:text-slate-600" title="<?php echo e(__('settings.cancel')); ?>">
                                                    <iconify-icon icon="solar:close-circle-bold" width="20"></iconify-icon>
                                                </button>
                                            </form>
                                            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('editingRoleName'),'class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('editingRoleName')),'class' => 'mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                                        <?php else: ?>
                                            
                                            <span class="h-2.5 w-2.5 rounded-full shrink-0 <?php echo e($selectedRole === $r->slug ? 'bg-[var(--accent)]' : 'bg-slate-300'); ?>"></span>

                                            
                                            <div class="flex-1 min-w-0 flex items-center gap-2">
                                                <span class="text-sm font-medium text-slate-900"><?php echo e($r->name); ?></span>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->is_default): ?>
                                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500"><?php echo e(__('settings.role_default_badge')); ?></span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>

                                            
                                            <span class="text-xs text-slate-400 tabular-nums"><?php echo e(trans_choice('settings.role_member_count', $roleMemberCounts[$r->slug] ?? 0, ['count' => $roleMemberCounts[$r->slug] ?? 0])); ?></span>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
                                                
                                                <button type="button" wire:click.stop="startEditRole(<?php echo e($r->id); ?>)" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-[var(--accent)] transition-all" title="<?php echo e(__('settings.edit')); ?>">
                                                    <iconify-icon icon="solar:pen-2-linear" width="16"></iconify-icon>
                                                </button>
                                                
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $r->is_default): ?>
                                                    <button type="button" @click.stop="$dispatch('confirm-action', { title: 'Supprimer', message: '<?php echo e(__('settings.delete_role_confirm')); ?>', confirmLabel: 'Supprimer', variant: 'danger', onConfirm: () => $wire.deleteRole(<?php echo e($r->id); ?>) })" class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-red-600 transition-all" title="<?php echo e(__('settings.delete')); ?>">
                                                        <iconify-icon icon="solar:trash-bin-trash-linear" width="16"></iconify-icon>
                                                    </button>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="content-card">
                    <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                        <?php
                            $selectedRoleDef = $roles->firstWhere('slug', $selectedRole);
                        ?>
                        <h2 class="text-lg font-bold text-slate-900"><?php echo e(__('settings.permissions_for', ['role' => $selectedRoleDef?->name ?? $selectedRole])); ?></h2>
                        <p class="text-sm text-slate-500"><?php echo e(__('settings.permissions_subtitle')); ?></p>
                    </div>

                    <div class="p-6">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($roles->isEmpty()): ?>
                            <div class="py-8 text-center text-slate-500 text-sm">
                                <?php echo e(__('settings.no_roles')); ?>

                            </div>
                        <?php elseif(! $selectedRoleDef): ?>
                            <div class="py-8 text-center text-slate-500 text-sm">
                                <?php echo e(__('settings.select_role_to_manage')); ?>

                            </div>
                        <?php else: ?>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedRole === 'owner'): ?>
                                <div class="rounded-xl bg-amber-50 p-4 text-sm text-amber-800 flex items-center gap-3 mb-6" style="border: 1px solid #fde68a;">
                                    <iconify-icon icon="solar:crown-bold-duotone" width="22" class="text-amber-600 shrink-0"></iconify-icon>
                                    <?php echo e(__('settings.owner_all_permissions')); ?>

                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php
                                $grouped = \App\Enums\Permission::grouped();
                                $groupIcons = [
                                    'tickets' => 'solar:ticket-bold-duotone',
                                    'team' => 'solar:users-group-rounded-bold-duotone',
                                    'forms' => 'solar:clipboard-list-bold-duotone',
                                    'settings' => 'solar:settings-bold-duotone',
                                    'reports' => 'solar:chart-2-bold-duotone',
                                    'discussions' => 'solar:inbox-bold-duotone',
                                ];
                            ?>

                            
                            <div class="rounded-xl overflow-hidden" style="border: 1px solid #e2e8f0;" x-data="{ openGroup: 'tickets' }">
                                <div class="divide-y divide-slate-100">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $permissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <div class="bg-white">
                                            <button
                                                type="button"
                                                @click="openGroup = openGroup === '<?php echo e($group); ?>' ? null : '<?php echo e($group); ?>'"
                                                class="w-full flex items-center justify-between gap-3 px-4 py-3 text-left rounded-lg hover:bg-slate-50 transition-colors"
                                            >
                                                <span class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                                                    <iconify-icon icon="<?php echo e($groupIcons[$group] ?? 'solar:widget-bold-duotone'); ?>" width="18" class="text-slate-500"></iconify-icon>
                                                    <?php echo e(__('permissions.group_' . $group)); ?>

                                                </span>
                                                <span class="text-slate-400 transition-transform" :class="openGroup === '<?php echo e($group); ?>' ? 'rotate-180' : ''">
                                                    <iconify-icon icon="solar:alt-arrow-down-linear" width="20"></iconify-icon>
                                                </span>
                                            </button>
                                            <div x-show="openGroup === '<?php echo e($group); ?>'" x-cloak class="border-t border-slate-100">
                                                <div class="overflow-x-auto">
                                                    <table class="w-full text-left border-collapse">
                                                        <tbody class="divide-y divide-slate-50">
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                                <?php
                                                                    $isGranted = $selectedRole === 'owner' || ($rolePermissions[$selectedRole][$perm->value] ?? false);
                                                                ?>
                                                                <tr class="hover:bg-slate-50/50 transition-colors">
                                                                    <td class="px-4 py-2 text-sm text-slate-900 pl-8">
                                                                        <?php echo e(__('permissions.' . $perm->value)); ?>

                                                                    </td>
                                                                    <td class="px-4 py-2 w-20 text-center">
                                                                        <label class="inline-flex items-center justify-center <?php echo e($selectedRole === 'owner' ? 'cursor-default opacity-80' : 'cursor-pointer'); ?>">
                                                                            <input
                                                                                type="checkbox"
                                                                                <?php if($isGranted): echo 'checked'; endif; ?>
                                                                                <?php if($selectedRole !== 'owner'): ?>
                                                                                    wire:click="toggleRolePermission('<?php echo e($selectedRole); ?>', '<?php echo e($perm->value); ?>')"
                                                                                <?php else: ?>
                                                                                    disabled
                                                                                <?php endif; ?>
                                                                                class="rounded border-slate-300 text-[var(--accent)] focus:ring-[var(--accent)]"
                                                                            />
                                                                        </label>
                                                                    </td>
                                                                </tr>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage && $selectedRole !== 'owner'): ?>
                                <div class="mt-6 pt-4 flex justify-end border-t border-slate-100">
                                    <button
                                        type="button"
                                        wire:click="saveRolePermissions"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[var(--accent)] px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:opacity-90 transition-all disabled:opacity-50"
                                    >
                                        <span wire:loading.remove wire:target="saveRolePermissions"><?php echo e(__('settings.save_permissions')); ?></span>
                                        <span wire:loading wire:target="saveRolePermissions"><iconify-icon icon="solar:refresh-linear" class="animate-spin" width="18"></iconify-icon></span>
                                    </button>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- MAINTENANCE TAB -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'maintenance'): ?>
            <div class="space-y-6">
                
                <div class="content-card">
                    <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                        <h2 class="text-lg font-bold text-slate-900"><?php echo e(__('settings.org_info_title')); ?></h2>
                        <p class="text-sm text-slate-500"><?php echo e(__('settings.org_info_subtitle')); ?></p>
                    </div>
                    <div class="p-6">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                            <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-100">
                                <iconify-icon icon="solar:buildings-2-bold-duotone" width="20" class="text-slate-400 shrink-0"></iconify-icon>
                                <div class="min-w-0">
                                    <span class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400"><?php echo e(__('settings.org_name_label')); ?></span>
                                    <span class="block text-sm font-medium text-slate-900 truncate"><?php echo e($org?->name ?? '-'); ?></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-100">
                                <iconify-icon icon="solar:link-round-bold-duotone" width="20" class="text-slate-400 shrink-0"></iconify-icon>
                                <div class="min-w-0">
                                    <span class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400"><?php echo e(__('settings.org_slug_label')); ?></span>
                                    <span class="block text-sm font-mono text-slate-900 truncate"><?php echo e($org?->slug ?? '-'); ?></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-100 sm:col-span-2">
                                <iconify-icon icon="solar:calendar-bold-duotone" width="20" class="text-slate-400 shrink-0"></iconify-icon>
                                <div class="min-w-0">
                                    <span class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400"><?php echo e(__('settings.org_created_label')); ?></span>
                                    <span class="block text-sm font-medium text-slate-900"><?php echo e($org?->created_at?->translatedFormat('d F Y à H:i') ?? '-'); ?></span>
                                </div>
                            </div>
                        </div>

                        
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <?php
                                $statItems = [
                                    ['key' => 'members', 'icon' => 'solar:users-group-rounded-bold-duotone', 'label' => __('settings.org_members_label'), 'color' => 'text-blue-500'],
                                    ['key' => 'tickets', 'icon' => 'solar:ticket-bold-duotone', 'label' => __('settings.org_tickets_label'), 'color' => 'text-amber-500'],
                                    ['key' => 'forms', 'icon' => 'solar:clipboard-text-bold-duotone', 'label' => __('settings.org_forms_label'), 'color' => 'text-violet-500'],
                                    ['key' => 'categories', 'icon' => 'solar:tag-bold-duotone', 'label' => __('settings.org_categories_label'), 'color' => 'text-emerald-500'],
                                    ['key' => 'priorities', 'icon' => 'solar:flag-bold-duotone', 'label' => __('settings.org_priorities_label'), 'color' => 'text-rose-500'],
                                    ['key' => 'roles', 'icon' => 'solar:shield-keyhole-bold-duotone', 'label' => __('settings.org_roles_label'), 'color' => 'text-cyan-500'],
                                ];
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $statItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-100 bg-white hover:bg-slate-50/50 transition-colors">
                                    <iconify-icon icon="<?php echo e($stat['icon']); ?>" width="22" class="<?php echo e($stat['color']); ?> shrink-0"></iconify-icon>
                                    <div>
                                        <span class="block text-lg font-bold text-slate-900 tabular-nums"><?php echo e(number_format($maintenanceStats[$stat['key']] ?? 0)); ?></span>
                                        <span class="block text-[11px] text-slate-500 font-medium"><?php echo e($stat['label']); ?></span>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div>
                </div>

                
                <div class="content-card">
                    <div class="px-6 py-5 bg-slate-50/50" style="border-bottom: 1px solid #f1f5f9;">
                        <h2 class="text-lg font-bold text-slate-900"><?php echo e(__('settings.cache_title')); ?></h2>
                        <p class="text-sm text-slate-500"><?php echo e(__('settings.cache_subtitle')); ?></p>
                    </div>
                    <div class="p-6 space-y-4">
                        
                        <div class="flex items-start sm:items-center justify-between gap-4 p-4 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors flex-col sm:flex-row">
                            <div class="flex items-start gap-3">
                                <iconify-icon icon="solar:database-bold-duotone" width="22" class="text-amber-500 mt-0.5 shrink-0"></iconify-icon>
                                <div>
                                    <span class="block text-sm font-semibold text-slate-900"><?php echo e(__('settings.clear_org_cache')); ?></span>
                                    <span class="block text-xs text-slate-500 mt-0.5 leading-relaxed"><?php echo e(__('settings.clear_org_cache_help')); ?></span>
                                </div>
                            </div>
                            <button
                                type="button"
                                @click="$dispatch('confirm-action', { title: 'Vider le cache', message: 'Vider le cache de l\u0027organisation ?', confirmLabel: 'Confirmer', variant: 'warning', onConfirm: () => $wire.clearOrganizationCache() })"
                                class="shrink-0 inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-100 transition-all"
                                <?php if(! $canManage): echo 'disabled'; endif; ?>
                            >
                                <span wire:loading.remove wire:target="clearOrganizationCache">
                                    <iconify-icon icon="solar:trash-bin-minimalistic-linear" width="16"></iconify-icon>
                                    <?php echo e(__('settings.clear_org_cache')); ?>

                                </span>
                                <span wire:loading wire:target="clearOrganizationCache">
                                    <iconify-icon icon="solar:refresh-linear" class="animate-spin" width="16"></iconify-icon>
                                </span>
                            </button>
                        </div>

                        
                        <div class="flex items-start sm:items-center justify-between gap-4 p-4 rounded-xl border border-slate-100 hover:border-slate-200 transition-colors flex-col sm:flex-row">
                            <div class="flex items-start gap-3">
                                <iconify-icon icon="solar:code-bold-duotone" width="22" class="text-blue-500 mt-0.5 shrink-0"></iconify-icon>
                                <div>
                                    <span class="block text-sm font-semibold text-slate-900"><?php echo e(__('settings.clear_view_cache')); ?></span>
                                    <span class="block text-xs text-slate-500 mt-0.5 leading-relaxed"><?php echo e(__('settings.clear_view_cache_help')); ?></span>
                                </div>
                            </div>
                            <button
                                type="button"
                                @click="$dispatch('confirm-action', { title: 'Vider le cache', message: 'Vider le cache des vues ?', confirmLabel: 'Confirmer', variant: 'warning', onConfirm: () => $wire.clearViewCache() })"
                                class="shrink-0 inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100 transition-all"
                                <?php if(! $canManage): echo 'disabled'; endif; ?>
                            >
                                <span wire:loading.remove wire:target="clearViewCache">
                                    <iconify-icon icon="solar:trash-bin-minimalistic-linear" width="16"></iconify-icon>
                                    <?php echo e(__('settings.clear_view_cache')); ?>

                                </span>
                                <span wire:loading wire:target="clearViewCache">
                                    <iconify-icon icon="solar:refresh-linear" class="animate-spin" width="16"></iconify-icon>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- DANGER ZONE -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'danger'): ?>
            <div class="rounded-xl sm:rounded-2xl bg-red-50 overflow-hidden" style="border: 1px solid #fecaca;">
                <div class="px-6 py-5 bg-red-100/50" style="border-bottom: 1px solid #fecaca;">
                    <h2 class="text-lg font-bold text-red-900"><?php echo e(__('settings.danger_title')); ?></h2>
                    <p class="text-sm text-red-700"><?php echo e(__('settings.danger_subtitle')); ?></p>
                </div>
                <div class="p-6">
                    <div class="rounded-xl bg-white p-6" style="border: 1px solid #fecaca;">
                        <h3 class="text-base font-bold text-slate-900"><?php echo e(__('settings.delete_organization')); ?></h3>
                        <p class="text-sm text-slate-500 mt-1 mb-4"><?php echo e(__('settings.delete_organization_help')); ?></p>

                        <div class="flex items-end gap-4">
                            <div class="flex-1">
                                <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'confirm_delete','value' => __('settings.confirm_delete_label')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'confirm_delete','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.confirm_delete_label'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginal18c21970322f9e5c938bc954620c12bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal18c21970322f9e5c938bc954620c12bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.text-input','data' => ['id' => 'confirm_delete','type' => 'text','class' => 'mt-1 w-full','wire:model.blur' => 'dangerConfirmName','placeholder' => ''.e($org?->name).'','disabled' => ! $isOwner]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'confirm_delete','type' => 'text','class' => 'mt-1 w-full','wire:model.blur' => 'dangerConfirmName','placeholder' => ''.e($org?->name).'','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(! $isOwner)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $attributes = $__attributesOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__attributesOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $component = $__componentOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__componentOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
                            </div>
                            <button
                                type="button"
                                @click="$dispatch('confirm-action', { title: '<?php echo e(__('Supprimer d\u00e9finitivement')); ?>', message: '<?php echo e(__('Cette action est irr\u00e9versible. Toutes les donn\u00e9es seront supprim\u00e9es.')); ?>', confirmLabel: '<?php echo e(__('Supprimer')); ?>', variant: 'danger', onConfirm: () => $wire.deleteOrganization() })"
                                class="h-[42px] px-4 rounded-xl bg-red-600 text-white text-sm font-bold shadow-md hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                <?php if(! $isOwner || $dangerConfirmName !== $org?->name): echo 'disabled'; endif; ?>
                            >
                                <?php echo e(__('settings.delete_permanently')); ?>

                            </button>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isOwner): ?>
                            <p class="text-xs text-red-600 mt-2 font-medium"><?php echo e(__('settings.owner_only')); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/livewire/admin/settings.blade.php ENDPATH**/ ?>