<?php
    $roleBadge = function (string $role): array {
        return match ($role) {
            'owner' => ['label_key' => 'pages.team.role_owner', 'bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-100', 'icon' => 'solar:crown-bold-duotone'],
            'admin' => ['label_key' => 'pages.team.role_admin', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100', 'icon' => 'solar:shield-check-bold-duotone'],
            'agent' => ['label_key' => 'pages.team.role_agent', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-100', 'icon' => 'solar:headphones-round-sound-bold-duotone'],
            'member' => ['label_key' => 'pages.team.role_member', 'bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'border' => 'border-slate-100', 'icon' => 'solar:user-bold-duotone'],
            default => ['label_key' => null, 'label' => $role, 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-100', 'icon' => 'solar:star-bold-duotone'],
        };
    };
    $inviteRoleDropdownOptions = $roles
        ->filter(fn ($r) => $r->slug !== 'owner')
        ->map(fn ($r) => ['value' => $r->slug, 'label' => $r->name])
        ->values()
        ->all();
    $inviteRoleLabel = $roles->firstWhere('slug', $inviteRole)?->name ?? __('pages.team.role');
?>

<div class="w-full max-w-full min-w-0 mx-auto" wire:init="loadPage">

    
    <div class="page-header">
        <div class="min-w-0">
            <h1 class="page-title"><?php echo e(__('pages.team.title')); ?></h1>
            <p class="page-subtitle"><?php echo e(__('pages.team.subtitle')); ?></p>
        </div>
        <div class="page-actions">
            <button
                type="button"
                wire:click="openInviteModal"
                wire:loading.attr="disabled"
                wire:target="openInviteModal"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-[var(--accent)] px-4 py-3 sm:py-2.5 text-sm font-bold text-white shadow-lg shadow-[var(--accent-ring)] hover:opacity-90 transition-all transform hover:-translate-y-0.5 touch-target sm:min-h-0 sm:min-w-0 w-full sm:w-auto"
            >
                <iconify-icon icon="solar:user-plus-bold" width="18"></iconify-icon>
                <?php echo e(__('pages.team.invite_member')); ?>

            </button>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $ready): ?>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 5; $i++): ?>
            <div class="stat-card animate-pulse">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0 space-y-2">
                        <div class="h-3 w-16 rounded bg-slate-200"></div>
                        <div class="h-7 w-10 rounded bg-slate-200"></div>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-slate-100"></div>
                </div>
            </div>
        <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="content-card animate-pulse">
        <div class="filter-bar">
            <div class="filter-bar-row">
                <div class="h-10 flex-1 rounded-xl bg-slate-100"></div>
                <div class="h-10 w-36 rounded-xl bg-slate-100"></div>
            </div>
        </div>
        <div class="divide-y divide-slate-100">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 5; $i++): ?>
                <div class="flex items-center gap-4 px-6 py-4">
                    <div class="h-10 w-10 rounded-full bg-slate-200"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-4 w-40 rounded bg-slate-200"></div>
                        <div class="h-3 w-56 rounded bg-slate-100"></div>
                    </div>
                    <div class="h-6 w-20 rounded-full bg-slate-100"></div>
                </div>
            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php else: ?>

    
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <div class="stat-card">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('pages.team.owners')); ?></span>
                    <div class="stat-card-value"><?php echo e($stats['owners'] ?? 0); ?></div>
                </div>
                <div class="stat-card-icon bg-purple-50 text-purple-600">
                    <iconify-icon icon="solar:crown-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('pages.team.admins')); ?></span>
                    <div class="stat-card-value"><?php echo e($stats['admins'] ?? 0); ?></div>
                </div>
                <div class="stat-card-icon bg-blue-50 text-blue-600">
                    <iconify-icon icon="solar:shield-check-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('pages.team.agents')); ?></span>
                    <div class="stat-card-value"><?php echo e($stats['agents'] ?? 0); ?></div>
                </div>
                <div class="stat-card-icon bg-amber-50 text-amber-600">
                    <iconify-icon icon="solar:headphones-round-sound-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('pages.team.members')); ?></span>
                    <div class="stat-card-value"><?php echo e($stats['members'] ?? 0); ?></div>
                </div>
                <div class="stat-card-icon bg-slate-100 text-slate-600">
                    <iconify-icon icon="solar:users-group-rounded-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <span class="stat-card-label"><?php echo e(__('pages.team.pending_count')); ?></span>
                    <div class="stat-card-value"><?php echo e($stats['pending_invites'] ?? 0); ?></div>
                </div>
                <div class="stat-card-icon bg-orange-50 text-orange-500">
                    <iconify-icon icon="solar:clock-circle-bold-duotone" width="20"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingInvitations->isNotEmpty()): ?>
        <div class="rounded-xl sm:rounded-2xl overflow-hidden mb-6 sm:mb-8" style="border: 1px solid #fed7aa; background: rgba(255, 247, 237, 0.3);">
            <div class="p-4 flex items-center gap-3" style="border-bottom: 1px solid #ffedd5; background: rgba(255, 237, 213, 0.5);">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-100 text-orange-600">
                    <iconify-icon icon="solar:letter-bold-duotone" width="18"></iconify-icon>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900"><?php echo e(__('pages.team.invitations_pending')); ?></h3>
                    <p class="text-xs text-slate-500"><?php echo e($pendingInvitations->count()); ?> <?php echo e(__('pages.team.invitation_pending')); ?></p>
                </div>
            </div>

            <div class="divide-y divide-orange-100">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pendingInvitations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <?php $ib = $roleBadge($inv->role); ?>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 hover:bg-orange-50/40 transition-colors">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-orange-100 text-orange-600">
                                <iconify-icon icon="<?php echo e($inv->email ? 'solar:letter-linear' : 'solar:key-minimalistic-square-3-linear'); ?>" width="20"></iconify-icon>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-slate-900 truncate">
                                    <?php echo e($inv->email ?: __('pages.team.invitation_by_code_label')); ?>

                                </div>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($inv->invitation_code)): ?>
                                        <span class="pill-badge bg-slate-100 text-slate-700 border-slate-200" style="font-size: 10px;">
                                            <?php echo e(__('pages.team.code')); ?>: <?php echo e($inv->invitation_code); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <span class="pill-badge <?php echo e($ib['bg']); ?> <?php echo e($ib['text']); ?> <?php echo e($ib['border']); ?>" style="font-size: 10px;">
                                        <iconify-icon icon="<?php echo e($ib['icon']); ?>" width="12"></iconify-icon>
                                        <?php echo e($ib['label_key'] ? __($ib['label_key']) : ($ib['label'] ?? $inv->role)); ?>

                                    </span>
                                    <span class="text-[10px] text-slate-400">
                                        <?php echo e(__('pages.team.invited_on', ['date' => $inv->created_at->format('d/m/Y')])); ?>

                                    </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inv->inviter): ?>
                                        <span class="text-[10px] text-slate-400">
                                            &middot; <?php echo e(__('pages.team.invited_by', ['name' => $inv->inviter->name])); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <span class="text-[10px] text-orange-600 font-medium">
                                        <?php echo e(__('pages.team.expires_in', ['days' => (int) now()->diffInDays($inv->expires_at)])); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 sm:ml-4">
                            <button
                                type="button"
                                wire:click="resendInvitation(<?php echo e($inv->id); ?>)"
                                wire:loading.attr="disabled"
                                wire:target="resendInvitation(<?php echo e($inv->id); ?>)"
                                class="h-8 px-3 rounded-lg bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition inline-flex items-center gap-1.5"
                                style="border: 1px solid #e2e8f0;"
                                title="<?php echo e($inv->email ? __('pages.team.resend') : __('pages.team.regenerate_code')); ?>"
                            >
                                <iconify-icon icon="solar:refresh-linear" width="14"></iconify-icon>
                                <?php echo e($inv->email ? __('pages.team.resend') : __('pages.team.regenerate_code')); ?>

                            </button>
                            <button
                                type="button"
                                @click="$dispatch('confirm-action', { title: 'Annuler', message: 'Annuler cette invitation ?', confirmLabel: 'Annuler', variant: 'danger', onConfirm: () => $wire.cancelInvitation(<?php echo e($inv->id); ?>) })"
                                wire:loading.attr="disabled"
                                wire:target="cancelInvitation(<?php echo e($inv->id); ?>)"
                                class="h-8 px-3 rounded-lg bg-white text-xs font-semibold text-red-600 hover:bg-red-50 transition inline-flex items-center gap-1.5"
                                style="border: 1px solid #fecaca;"
                                title="<?php echo e(__('pages.team.cancel_invitation')); ?>"
                            >
                                <iconify-icon icon="solar:close-circle-linear" width="14"></iconify-icon>
                                <?php echo e(__('pages.team.cancel_invitation')); ?>

                            </button>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="flex items-center gap-1 p-1 rounded-xl bg-slate-100/80 mb-5 w-fit">
        <button
            type="button"
            wire:click="setTeamTab('internal')"
            wire:loading.attr="disabled"
            wire:target="setTeamTab"
            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200',
                'bg-white text-slate-900 shadow-sm' => ($teamTab ?? 'internal') === 'internal',
                'text-slate-500 hover:text-slate-700' => ($teamTab ?? 'internal') !== 'internal',
            ]); ?>"
        >
            <iconify-icon icon="solar:users-group-rounded-bold-duotone" width="18"></iconify-icon>
            <?php echo e(__('pages.team.tab_internal')); ?>

            <span
                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    'inline-flex items-center justify-center min-w-[22px] h-[22px] px-1.5 rounded-full text-[11px] font-bold transition-colors',
                    'bg-[var(--accent)] text-white' => ($teamTab ?? 'internal') === 'internal',
                    'bg-slate-200 text-slate-600' => ($teamTab ?? 'internal') !== 'internal',
                ]); ?>"
            ><?php echo e($stats['total'] ?? 0); ?></span>
        </button>
        <button
            type="button"
            wire:click="setTeamTab('external')"
            wire:loading.attr="disabled"
            wire:target="setTeamTab"
            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200',
                'bg-white text-slate-900 shadow-sm' => ($teamTab ?? 'internal') === 'external',
                'text-slate-500 hover:text-slate-700' => ($teamTab ?? 'internal') !== 'external',
            ]); ?>"
        >
            <iconify-icon icon="solar:mailbox-bold-duotone" width="18"></iconify-icon>
            <?php echo e(__('pages.team.tab_external')); ?>

            <span
                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    'inline-flex items-center justify-center min-w-[22px] h-[22px] px-1.5 rounded-full text-[11px] font-bold transition-colors',
                    'bg-[var(--accent)] text-white' => ($teamTab ?? 'internal') === 'external',
                    'bg-slate-200 text-slate-600' => ($teamTab ?? 'internal') !== 'external',
                ]); ?>"
            ><?php echo e($stats['external'] ?? 0); ?></span>
        </button>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($teamTab ?? 'internal') === 'internal'): ?>
    <div>
        <div class="content-card">
            <?php
                $roleFilterOptions = array_merge(
                    [['value' => '', 'label' => __('pages.team.all_roles')]],
                    $roles->map(fn ($r) => ['value' => $r->slug, 'label' => $r->name])->all()
                );
                $roleFilterLabel = $role === '' ? __('pages.team.all_roles') : ($roles->firstWhere('slug', $role)?->name ?? $role);
                $perPageOptions = [
                    ['value' => 10, 'label' => '10'],
                    ['value' => 25, 'label' => '25'],
                    ['value' => 50, 'label' => '50'],
                ];
                $functionRowOptions = array_merge(
                    [['value' => '', 'label' => __('pages.team.no_function')]],
                    $organizationFunctions->map(fn ($fn) => ['value' => (string) $fn->id, 'label' => $fn->name])->all()
                );
                $memberRoleOptions = $roles->map(fn ($r) => ['value' => $r->slug, 'label' => $r->name])->all();
            ?>
            
            <div class="filter-bar">
                <div class="filter-bar-row">
                    <div class="flex-1 relative">
                        <iconify-icon icon="solar:magnifer-linear" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300" width="18"></iconify-icon>
                        <input
                            type="text"
                            class="filter-search"
                            placeholder="<?php echo e(__('pages.team.search_placeholder')); ?>"
                            wire:model.live.debounce.500ms="search"
                            wire:loading.attr="disabled"
                            wire:target="search"
                        />
                    </div>
                    <div class="filter-controls">
                        <div class="w-44 min-w-[10rem]">
                            <?php if (isset($component)) { $__componentOriginal67b35608722bcee218637ec24a02b934 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67b35608722bcee218637ec24a02b934 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-select','data' => ['options' => $roleFilterOptions,'label' => $roleFilterLabel,'selectedValue' => $role,'modelName' => 'role']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($roleFilterOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($roleFilterLabel),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($role),'model-name' => 'role']); ?>
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
                        </div>
                        <div class="w-24 min-w-[5.5rem]">
                            <?php if (isset($component)) { $__componentOriginal67b35608722bcee218637ec24a02b934 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67b35608722bcee218637ec24a02b934 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-select','data' => ['options' => $perPageOptions,'label' => (string) $perPage,'selectedValue' => $perPage,'modelName' => 'perPage']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($perPageOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((string) $perPage),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($perPage),'model-name' => 'perPage']); ?>
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
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th><?php echo e(__('pages.team.member')); ?></th>
                            <th><?php echo e(__('pages.team.role')); ?></th>
                            <th><?php echo e(__('pages.team.business_function')); ?></th>
                            <th class="text-right"><?php echo e(__('pages.team.actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $memberships; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <?php $b = $roleBadge($m->role); ?>
                            <tr class="group">
                                <td>
                                    <div class="flex items-center gap-4">
                                        <?php if (isset($component)) { $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.avatar','data' => ['name' => $m->user?->name ?? 'U','size' => 'h-10 w-10','class' => 'ring-2 ring-white shadow-sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($m->user?->name ?? 'U'),'size' => 'h-10 w-10','class' => 'ring-2 ring-white shadow-sm']); ?>
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
                                        <div>
                                            <div class="text-sm font-bold text-slate-900"><?php echo e($m->user?->name ?? __('pages.team.unknown_user')); ?></div>
                                            <div class="text-xs text-slate-500 mt-0.5"><?php echo e($m->user?->email ?? ''); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="pill-badge <?php echo e($b['bg']); ?> <?php echo e($b['text']); ?> <?php echo e($b['border']); ?>">
                                        <iconify-icon icon="<?php echo e($b['icon']); ?>" width="14"></iconify-icon>
                                        <?php echo e($b['label_key'] ? __($b['label_key']) : ($b['label'] ?? $m->role)); ?>

                                    </span>
                                </td>
                                <td class="min-w-[140px]">
                                    <?php if (isset($component)) { $__componentOriginal67b35608722bcee218637ec24a02b934 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67b35608722bcee218637ec24a02b934 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-select','data' => ['options' => $functionRowOptions,'label' => $organizationFunctions->firstWhere('id', $m->organization_function_id)?->name ?? __('pages.team.no_function'),'selectedValue' => $m->organization_function_id !== null ? (string) $m->organization_function_id : '','wireMethod' => 'updateFunction','wireTargetId' => (int) $m->id,'instanceKey' => 'fn-' . $m->id,'compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($functionRowOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($organizationFunctions->firstWhere('id', $m->organization_function_id)?->name ?? __('pages.team.no_function')),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($m->organization_function_id !== null ? (string) $m->organization_function_id : ''),'wire-method' => 'updateFunction','wire-target-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((int) $m->id),'instance-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('fn-' . $m->id),'compact' => true]); ?>
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
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <div class="min-w-[8.5rem]">
                                            <?php if (isset($component)) { $__componentOriginal67b35608722bcee218637ec24a02b934 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67b35608722bcee218637ec24a02b934 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-select','data' => ['options' => $memberRoleOptions,'label' => $roles->firstWhere('slug', $m->role)?->name ?? $m->role,'selectedValue' => $m->role,'wireMethod' => 'updateRole','wireTargetId' => (int) $m->id,'instanceKey' => 'role-' . $m->id,'compact' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($memberRoleOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($roles->firstWhere('slug', $m->role)?->name ?? $m->role),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($m->role),'wire-method' => 'updateRole','wire-target-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((int) $m->id),'instance-key' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('role-' . $m->id),'compact' => true]); ?>
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
                                        </div>

                                        <button
                                            type="button"
                                            class="h-8 w-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                            @click="$dispatch('confirm-action', { title: 'Retirer', message: 'Retirer ce membre de l\u0027\u00e9quipe ?', confirmLabel: 'Retirer', variant: 'danger', onConfirm: () => $wire.removeMember(<?php echo e((int) $m->id); ?>) })"
                                            title="<?php echo e(__('pages.team.remove_from_team')); ?>"
                                        >
                                            <iconify-icon icon="solar:trash-bin-trash-bold" width="16"></iconify-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <iconify-icon icon="solar:users-group-rounded-linear" width="28" class="text-slate-300"></iconify-icon>
                                        </div>
                                        <p class="empty-state-title"><?php echo e(__('pages.team.no_members_found')); ?></p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <div class="px-6 py-4 bg-slate-50/30" style="border-top: 1px solid #f1f5f9;">
                <?php echo e($memberships->links()); ?>

            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(($teamTab ?? 'internal') === 'external'): ?>
    <div>
        <div class="content-card">
            
            <div class="filter-bar">
                <div class="filter-bar-row">
                    <div class="flex-1 relative">
                        <iconify-icon icon="solar:magnifer-linear" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300" width="18"></iconify-icon>
                        <input
                            type="text"
                            class="filter-search"
                            placeholder="<?php echo e(__('pages.team.search_external_placeholder')); ?>"
                            wire:model.live.debounce.500ms="searchExternal"
                            wire:loading.attr="disabled"
                            wire:target="searchExternal"
                        />
                    </div>
                </div>
            </div>

            
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th><?php echo e(__('pages.team.member')); ?></th>
                            <th><?php echo e(__('pages.team.external_col_source')); ?></th>
                            <th><?php echo e(__('pages.team.external_col_first_contact')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $externalContacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <tr>
                                <td>
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <?php if (isset($component)) { $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.avatar','data' => ['name' => $ec->user?->name ?? 'U','size' => 'h-10 w-10','class' => 'ring-2 ring-white shadow-sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($ec->user?->name ?? 'U'),'size' => 'h-10 w-10','class' => 'ring-2 ring-white shadow-sm']); ?>
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
                                            <div class="absolute -bottom-0.5 -right-0.5 h-4 w-4 rounded-full bg-slate-100 flex items-center justify-center ring-2 ring-white">
                                                <iconify-icon icon="solar:letter-bold" width="10" class="text-slate-400"></iconify-icon>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-900"><?php echo e($ec->user?->name ?? __('pages.team.unknown_user')); ?></div>
                                            <div class="text-xs text-slate-500 mt-0.5"><?php echo e($ec->user?->email ?? ''); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="pill-badge bg-sky-50 text-sky-600 border-sky-100">
                                        <iconify-icon icon="solar:letter-bold-duotone" width="14"></iconify-icon>
                                        <?php echo e(__('pages.team.external_source')); ?>

                                    </span>
                                </td>
                                <td class="text-sm text-slate-500">
                                    <?php echo e($ec->created_at?->format('d/m/Y') ?? '—'); ?>

                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <iconify-icon icon="solar:mailbox-linear" width="28" class="text-slate-300"></iconify-icon>
                                        </div>
                                        <p class="empty-state-title"><?php echo e(__('pages.team.no_external_contacts')); ?></p>
                                        <p class="text-xs text-slate-400 mt-1"><?php echo e(__('pages.team.no_external_contacts_hint')); ?></p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <div class="px-6 py-4 bg-slate-50/30" style="border-top: 1px solid #f1f5f9;">
                <?php echo e($externalContacts->links()); ?>

            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> 

    
    <template x-teleport="<?php echo e('body'); ?>">
    <div
        x-data="{
            open: $wire.$entangle('showInviteModal'),
            inviteUi: 'email',
            _prevInviteOpen: false,
        }"
        x-effect="
            if (open && ! _prevInviteOpen) { inviteUi = 'email' }
            _prevInviteOpen = open
        "
        x-show="open"
        x-cloak
        @keydown.escape.window="if (open) $wire.closeInviteModal()"
        class="fixed inset-0 z-[100] flex items-end justify-center sm:items-center p-0 sm:p-4"
    >
        <div
            x-show="open"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
            @click="$wire.closeInviteModal()"
        ></div>

        <div
            x-show="open"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative z-10 flex max-h-[min(92dvh,100dvh)] w-full max-w-lg flex-col overflow-hidden rounded-t-3xl border border-slate-200 bg-white shadow-2xl sm:max-h-[min(90dvh,40rem)] sm:rounded-2xl"
            @click.stop
        >
            <div class="flex shrink-0 items-center justify-between border-b border-slate-100 px-5 py-4">
                <div class="min-w-0">
                    <div class="text-sm font-extrabold text-slate-900"><?php echo e(__('pages.team.invite_member')); ?></div>
                    <div class="text-xs text-slate-500"><?php echo e(__('pages.team.invite_modal_subtitle')); ?></div>
                </div>
                <button type="button" wire:click="closeInviteModal" class="h-9 w-9 rounded-xl bg-white text-slate-500 hover:bg-slate-50 transition flex items-center justify-center" style="border: 1px solid #e2e8f0;" aria-label="<?php echo e(__('pages.team.close')); ?>">
                    <iconify-icon icon="solar:close-circle-linear" width="18"></iconify-icon>
                </button>
            </div>

            
            <form class="flex min-h-0 flex-1 flex-col overflow-hidden" x-on:submit.prevent="$wire.sendInvite(inviteUi)">
                <div class="min-h-0 flex-1 space-y-4 overflow-y-auto overscroll-y-contain px-5 py-4">
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-semibold text-slate-700"><?php echo e(__('pages.team.invitation_mode')); ?></label>
                        <div class="grid grid-cols-2 gap-2">
                            <button
                                type="button"
                                @click="inviteUi = 'email'"
                                class="h-10 rounded-xl text-sm font-semibold transition"
                                style="border: 1px solid #e2e8f0;"
                                x-bind:class="inviteUi === 'email' ? 'bg-[var(--accent)] text-white' : 'bg-white text-slate-700'"
                            >
                                <?php echo e(__('pages.team.invite_by_email')); ?>

                            </button>
                            <button
                                type="button"
                                @click="inviteUi = 'code'"
                                class="h-10 rounded-xl text-sm font-semibold transition"
                                style="border: 1px solid #e2e8f0;"
                                x-bind:class="inviteUi === 'code' ? 'bg-[var(--accent)] text-white' : 'bg-white text-slate-700'"
                            >
                                <?php echo e(__('pages.team.invite_by_code')); ?>

                            </button>
                        </div>
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('inviteMethod')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('inviteMethod'))]); ?>
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

                    <div class="space-y-1.5" x-show="inviteUi === 'email'" x-cloak>
                        <label class="text-[11px] font-semibold text-slate-700"><?php echo e(__('pages.team.email')); ?></label>
                        <input
                            type="email"
                            wire:model.blur="inviteEmail"
                            class="block w-full rounded-xl bg-white py-2.5 px-3 text-sm shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)]"
                            style="border: 1px solid #e2e8f0;"
                            placeholder="<?php echo e(__('pages.team.email_placeholder')); ?>"
                        >
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('inviteEmail')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('inviteEmail'))]); ?>
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

                    <div class="space-y-1.5">
                        <label class="text-[11px] font-semibold text-slate-700"><?php echo e(__('pages.team.role')); ?></label>
                        <?php if (isset($component)) { $__componentOriginal67b35608722bcee218637ec24a02b934 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal67b35608722bcee218637ec24a02b934 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-select','data' => ['options' => $inviteRoleDropdownOptions,'label' => $inviteRoleLabel,'selectedValue' => $inviteRole,'modelName' => 'inviteRole','instanceKey' => 'invite-role']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inviteRoleDropdownOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inviteRoleLabel),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inviteRole),'model-name' => 'inviteRole','instance-key' => 'invite-role']); ?>
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
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('inviteRole')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('inviteRole'))]); ?>
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

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($generatedInviteCode)): ?>
                        <div class="rounded-xl bg-slate-50 px-3 py-3" style="border: 1px solid #e2e8f0;">
                            <div class="text-[11px] font-semibold text-slate-700 mb-1"><?php echo e(__('pages.team.generated_code')); ?></div>
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                <code class="inline-flex min-h-10 flex-1 items-center rounded-lg bg-white px-3 py-2 text-sm font-bold text-slate-900 tracking-wider break-all" style="border: 1px solid #e2e8f0;"><?php echo e($generatedInviteCode); ?></code>
                                <button type="button" x-on:click="navigator.clipboard.writeText('<?php echo e($generatedInviteCode); ?>')" class="inline-flex h-10 shrink-0 items-center justify-center rounded-lg bg-white px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition sm:w-auto w-full" style="border: 1px solid #e2e8f0;">
                                    <?php echo e(__('pages.team.copy_code')); ?>

                                </button>
                            </div>
                            <p class="mt-2 text-[11px] text-slate-500"><?php echo e(__('pages.team.code_expiry_hint')); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="flex shrink-0 flex-col gap-2 border-t border-slate-100 bg-white px-5 py-4 pb-[max(1rem,env(safe-area-inset-bottom))] sm:flex-row sm:items-center sm:justify-end sm:gap-3 sm:pb-4">
                    <button type="button" wire:click="closeInviteModal" class="h-10 w-full rounded-xl bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition sm:w-auto sm:px-4" style="border: 1px solid #e2e8f0;">
                        <?php echo e(__('pages.team.cancel')); ?>

                    </button>
                    <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','variant' => 'primary','wireTarget' => 'sendInvite','loadingLabel' => __('pages.team.sending'),'class' => 'h-10 w-full rounded-xl text-sm font-extrabold shadow-sm sm:w-auto sm:px-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary','wire-target' => 'sendInvite','loading-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('pages.team.sending')),'class' => 'h-10 w-full rounded-xl text-sm font-extrabold shadow-sm sm:w-auto sm:px-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <span x-show="inviteUi === 'email'"><?php echo e(__('pages.team.send')); ?></span>
                        <span x-show="inviteUi === 'code'"><?php echo e(__('pages.team.generate_code')); ?></span>
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
    </div>
    </template>

</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\admin\users.blade.php ENDPATH**/ ?>