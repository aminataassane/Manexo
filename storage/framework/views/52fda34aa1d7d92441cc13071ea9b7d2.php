<?php
    /** @var \Illuminate\Pagination\LengthAwarePaginator<int, \App\Models\User> $users */
    $users = get_defined_vars()['users'] ?? new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
?>
<div class="space-y-6 pb-12">
    
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900"><?php echo e(__('super_admin.users.title')); ?></h1>
            <p class="mt-1 text-sm text-slate-500"><?php echo e(__('super_admin.users.subtitle')); ?></p>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->canPlatformAdminister()): ?>
            <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['wire:click' => 'openInviteModal','wireTarget' => 'openInviteModal','variant' => 'super','class' => 'self-start']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'openInviteModal','wire-target' => 'openInviteModal','variant' => 'super','class' => 'self-start']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <iconify-icon icon="solar:letter-bold" width="16"></iconify-icon>
                <?php echo e(__('platform_invitations.invite_button')); ?>

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
    </header>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-center gap-2">
            <iconify-icon icon="solar:check-circle-bold" width="18"></iconify-icon>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(auth()->user()->canPlatformAdminister() && $pendingInvitations->count() > 0): ?>
        <div class="rounded-2xl border border-[#005F02]/20 bg-[#F2E3BB]/20 overflow-hidden">
            <button
                wire:click="$toggle('showPendingInvitations')"
                class="w-full flex items-center justify-between px-5 py-3 text-sm font-semibold text-[#005F02] hover:bg-[#F2E3BB]/20 transition-colors"
            >
                <div class="flex items-center gap-2">
                    <iconify-icon icon="solar:letter-opened-bold-duotone" width="18" class="text-[#005F02]"></iconify-icon>
                    <?php echo e(__('platform_invitations.pending_title')); ?>

                    <span class="inline-flex items-center rounded-full bg-[#F2E3BB]/30 border border-[#005F02]/20 px-2 py-0.5 text-[10px] font-bold text-[#005F02]">
                        <?php echo e($pendingInvitations->count()); ?>

                    </span>
                </div>
                <iconify-icon icon="<?php echo e($showPendingInvitations ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'); ?>" width="14" class="text-[#005F02]/70"></iconify-icon>
            </button>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPendingInvitations): ?>
                <div class="border-t border-[#005F02]/20 divide-y divide-[#005F02]/10">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pendingInvitations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="flex items-center justify-between px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full bg-[#F2E3BB]/30 flex items-center justify-center">
                                    <iconify-icon icon="solar:letter-bold" width="14" class="text-[#005F02]"></iconify-icon>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-slate-800"><?php echo e($inv->email); ?></div>
                                    <div class="text-xs text-slate-500">
                                        <?php echo e($inv->platform_role->label()); ?>

                                        &middot; <?php echo e(__('platform_invitations.invited_by', ['name' => $inv->inviter?->name ?? '—'])); ?>

                                        &middot; <?php echo e(__('platform_invitations.expires_in', ['time' => $inv->expires_at->diffForHumans()])); ?>

                                    </div>
                                </div>
                            </div>
                            <button
                                @click="$dispatch('confirm-action', { title: 'Annuler', message: 'Annuler cette invitation ?', confirmLabel: 'Annuler', variant: 'danger', onConfirm: () => $wire.cancelInvitation(<?php echo e($inv->id); ?>) })"
                                class="sa-btn-ghost text-red-600 hover:!bg-red-50"
                            >
                                <iconify-icon icon="solar:close-circle-bold" width="14"></iconify-icon>
                                <?php echo e(__('super_admin.cancel')); ?>

                            </button>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php
        $globalUsersStatusOptions = [
            ['value' => '', 'label' => __('super_admin.users.all_statuses')],
            ['value' => 'active', 'label' => __('super_admin.users.status_active')],
            ['value' => 'deactivated', 'label' => __('super_admin.users.status_deactivated')],
        ];
        $globalUsersStatusLabel = collect($globalUsersStatusOptions)->firstWhere('value', (string) ($statusFilter ?? ''))['label'] ?? __('super_admin.users.all_statuses');
        $platformInviteRoleOptions = [
            ['value' => 'platform_admin', 'label' => __('platform_invitations.role_platform_admin')],
            ['value' => 'platform_observer', 'label' => __('platform_invitations.role_platform_observer')],
        ];
        $platformInviteRoleLabel = collect($platformInviteRoleOptions)->firstWhere('value', (string) ($inviteRole ?? ''))['label'] ?? '';
    ?>
    
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <iconify-icon icon="solar:magnifer-linear" width="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></iconify-icon>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="<?php echo e(__('super_admin.users.search_placeholder')); ?>"
                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 placeholder-slate-400 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all"
            />
        </div>
        <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['options' => $globalUsersStatusOptions,'label' => $globalUsersStatusLabel,'selectedValue' => $statusFilter ?? '','wire:model.live' => 'statusFilter']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($globalUsersStatusOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($globalUsersStatusLabel),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statusFilter ?? ''),'wire:model.live' => 'statusFilter']); ?>
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

    
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden" x-data="{ expanded: null }">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80">
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('super_admin.users.col_name')); ?></th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('super_admin.users.col_email')); ?></th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('super_admin.users.col_status')); ?></th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500"><?php echo e(__('super_admin.users.col_orgs')); ?></th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500 w-[130px]"><?php echo e(__('super_admin.users.col_last_login')); ?></th>
                        <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500 w-[40px]"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php
                            $status = $user->status ?? 'active';
                            $statusConf = match($status) {
                                'active'      => ['dot' => 'bg-emerald-500', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                'deactivated' => ['dot' => 'bg-red-400',     'badge' => 'bg-red-50 text-red-700 border-red-200'],
                                default       => ['dot' => 'bg-slate-400',   'badge' => 'bg-slate-50 text-slate-700 border-slate-200'],
                            };

                            $roleBadge = null;
                            if ($user->platform_role) {
                                $roleBadge = match($user->platform_role->value) {
                                    'super_admin'       => 'bg-purple-50 border-purple-200 text-purple-700',
                                    'platform_admin'    => 'bg-[#F2E3BB]/30 border-[#005F02]/20 text-[#005F02]',
                                    'platform_observer' => 'bg-sky-50 border-sky-200 text-sky-700',
                                    default             => null,
                                };
                            } elseif ($user->is_super_admin) {
                                $roleBadge = 'bg-purple-50 border-purple-200 text-purple-700';
                            }

                            $initials = collect(explode(' ', $user->name))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');
                            $avatarColors = ['bg-blue-100 text-blue-700', 'bg-emerald-100 text-emerald-700', 'bg-violet-100 text-violet-700', 'bg-amber-100 text-amber-700', 'bg-rose-100 text-rose-700', 'bg-teal-100 text-teal-700'];
                            $avatarColor = $avatarColors[$user->id % count($avatarColors)];
                        ?>

                        
                        <tr
                            class="border-b border-slate-100 transition-colors cursor-pointer"
                            :class="expanded === <?php echo e($user->id); ?> ? 'bg-slate-50' : 'hover:bg-slate-50/50'"
                            @click="expanded = expanded === <?php echo e($user->id); ?> ? null : <?php echo e($user->id); ?>"
                        >
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="relative shrink-0">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full text-[10px] font-bold <?php echo e($avatarColor); ?>">
                                            <?php echo e($initials); ?>

                                        </div>
                                        <span class="absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 rounded-full <?php echo e($statusConf['dot']); ?> ring-2 ring-white"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-sm font-medium text-slate-800 truncate"><?php echo e($user->name); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($roleBadge): ?>
                                                <span class="inline-flex items-center rounded-md border px-1.5 py-0.5 text-[9px] font-semibold <?php echo e($roleBadge); ?>">
                                                    <?php echo e($user->platform_role?->label() ?? __('super_admin.users.super_admin_badge')); ?>

                                                </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-sm text-slate-500"><?php echo e($user->email); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $user->email_verified_at): ?>
                                    <span class="ml-1 inline-flex items-center rounded-md bg-amber-50 border border-amber-200 px-1.5 py-0.5 text-[9px] font-semibold text-amber-700">
                                        <?php echo e(__('super_admin.users.not_verified')); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 rounded-full border px-2 py-0.5 text-[11px] font-semibold <?php echo e($statusConf['badge']); ?>">
                                    <span class="h-1.5 w-1.5 rounded-full <?php echo e($statusConf['dot']); ?>"></span>
                                    <?php echo e(__('super_admin.users.status_' . $status)); ?>

                                </span>
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-500">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->organizations->count() > 0): ?>
                                    <div class="flex flex-wrap gap-1">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $user->organizations->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $org): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                            <span class="inline-flex items-center rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-600">
                                                <?php echo e(Str::limit($org->name, 15)); ?>

                                            </span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <?php if($user->organizations->count() > 2): ?>
                                            <span class="text-[10px] text-slate-400 font-medium">+<?php echo e($user->organizations->count() - 2); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-slate-300">&mdash;</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-5 py-3">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->last_login_at): ?>
                                    <div class="text-xs text-slate-600"><?php echo e(\Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y')); ?></div>
                                    <div class="text-[11px] text-slate-400"><?php echo e(\Carbon\Carbon::parse($user->last_login_at)->format('H:i')); ?></div>
                                <?php else: ?>
                                    <span class="text-xs text-slate-300"><?php echo e(__('super_admin.users.never')); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div
                                    class="inline-flex items-center justify-center h-6 w-6 rounded-md transition-colors"
                                    :class="expanded === <?php echo e($user->id); ?> ? 'bg-[#005F02]/10 text-[#005F02]' : 'text-slate-300'"
                                >
                                    <iconify-icon
                                        :icon="expanded === <?php echo e($user->id); ?> ? 'solar:alt-arrow-up-linear' : 'solar:alt-arrow-down-linear'"
                                        width="14"
                                    ></iconify-icon>
                                </div>
                            </td>
                        </tr>

                        
                        <tr x-show="expanded === <?php echo e($user->id); ?>" x-cloak>
                            <td colspan="6" class="px-0 py-0">
                                <div
                                    x-show="expanded === <?php echo e($user->id); ?>"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="border-b border-slate-100 bg-slate-50/50 px-5 py-5"
                                >
                                    <div class="flex flex-col lg:flex-row gap-6">
                                        
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-3">
                                                <iconify-icon icon="solar:user-id-bold-duotone" width="16" class="text-[#005F02]"></iconify-icon>
                                                <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider"><?php echo e(__('super_admin.audit.col_details')); ?></span>
                                            </div>
                                            <div class="rounded-xl bg-white border border-slate-200 divide-y divide-slate-100 overflow-hidden">
                                                <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                                    <span class="shrink-0 w-[100px] font-medium text-slate-400"><?php echo e(__('super_admin.users.col_name')); ?></span>
                                                    <span class="text-slate-700 font-medium"><?php echo e($user->name); ?></span>
                                                </div>
                                                <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                                    <span class="shrink-0 w-[100px] font-medium text-slate-400"><?php echo e(__('super_admin.users.col_email')); ?></span>
                                                    <span class="text-slate-700"><?php echo e($user->email); ?></span>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $user->email_verified_at): ?>
                                                        <span class="inline-flex items-center rounded-md bg-amber-50 border border-amber-200 px-1.5 py-0.5 text-[9px] font-semibold text-amber-700"><?php echo e(__('super_admin.users.not_verified')); ?></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                                <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                                    <span class="shrink-0 w-[100px] font-medium text-slate-400"><?php echo e(__('super_admin.users.col_status')); ?></span>
                                                    <span class="inline-flex items-center gap-1.5 rounded-full border px-2 py-0.5 text-[10px] font-semibold <?php echo e($statusConf['badge']); ?>">
                                                        <span class="h-1.5 w-1.5 rounded-full <?php echo e($statusConf['dot']); ?>"></span>
                                                        <?php echo e(__('super_admin.users.status_' . $status)); ?>

                                                    </span>
                                                </div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->platform_role || $user->is_super_admin): ?>
                                                    <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                                        <span class="shrink-0 w-[100px] font-medium text-slate-400"><?php echo e(__('platform_invitations.role_label')); ?></span>
                                                        <span class="text-slate-700"><?php echo e($user->platform_role?->label() ?? __('super_admin.users.super_admin_badge')); ?></span>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <div class="flex items-baseline gap-4 px-4 py-2.5 text-xs">
                                                    <span class="shrink-0 w-[100px] font-medium text-slate-400"><?php echo e(__('super_admin.users.col_last_login')); ?></span>
                                                    <span class="text-slate-700">
                                                        <?php echo e($user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y H:i') . ' — ' . \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : __('super_admin.users.never')); ?>

                                                    </span>
                                                </div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->organizations->count() > 0): ?>
                                                    <div class="flex items-start gap-4 px-4 py-2.5 text-xs">
                                                        <span class="shrink-0 w-[100px] font-medium text-slate-400 pt-0.5"><?php echo e(__('super_admin.users.col_orgs')); ?></span>
                                                        <div class="flex flex-wrap gap-1.5">
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $user->organizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $org): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                                <span class="inline-flex items-center rounded-md bg-slate-100 border border-slate-200 px-2 py-0.5 text-[10px] font-medium text-slate-600">
                                                                    <?php echo e($org->name); ?>

                                                                </span>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>

                                        
                                        <div class="lg:w-[220px] shrink-0">
                                            <div class="flex items-center gap-2 mb-3">
                                                <iconify-icon icon="solar:settings-bold-duotone" width="16" class="text-[#005F02]"></iconify-icon>
                                                <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider"><?php echo e(__('super_admin.users.col_actions')); ?></span>
                                            </div>
                                            <div class="space-y-1.5" @click.stop>
                                                <?php if(auth()->user()->canPlatformManage()): ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($status === 'active'): ?>
                                                        <button
                                                            @click="$dispatch('confirm-action', { title: 'Suspendre', message: 'Suspendre cet utilisateur ?', confirmLabel: 'Suspendre', variant: 'danger', onConfirm: () => $wire.suspendUser(<?php echo e($user->id); ?>) })"
                                                            class="flex w-full items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-xs font-medium text-amber-700 bg-white border border-slate-200 hover:bg-amber-50 hover:border-amber-200 transition-colors"
                                                        >
                                                            <iconify-icon icon="solar:pause-circle-bold-duotone" width="16" class="text-amber-500"></iconify-icon>
                                                            <?php echo e(__('super_admin.users.suspend')); ?>

                                                        </button>
                                                    <?php else: ?>
                                                        <button
                                                            wire:click="activateUser(<?php echo e($user->id); ?>)"
                                                            class="flex w-full items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-xs font-medium text-emerald-700 bg-white border border-slate-200 hover:bg-emerald-50 hover:border-emerald-200 transition-colors"
                                                        >
                                                            <iconify-icon icon="solar:check-circle-bold-duotone" width="16" class="text-emerald-500"></iconify-icon>
                                                            <?php echo e(__('super_admin.users.activate')); ?>

                                                        </button>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $user->email_verified_at): ?>
                                                        <button
                                                            wire:click="forceVerifyEmail(<?php echo e($user->id); ?>)"
                                                            class="flex w-full items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-xs font-medium text-teal-700 bg-white border border-slate-200 hover:bg-teal-50 hover:border-teal-200 transition-colors"
                                                        >
                                                            <iconify-icon icon="solar:verified-check-bold-duotone" width="16" class="text-teal-500"></iconify-icon>
                                                            <?php echo e(__('super_admin.users.force_verify')); ?>

                                                        </button>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                <?php if(auth()->user()->canPlatformAdminister() && $user->hasPlatformAccess() && $user->id !== auth()->id()): ?>
                                                    
                                                    <div class="pt-2 mt-2 border-t border-slate-200">
                                                        <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1.5 px-1"><?php echo e(__('platform_invitations.change_role')); ?></div>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = \App\Enums\PlatformRole::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                            <?php
                                                                $isCurrent = $user->platform_role === $role || ($role === \App\Enums\PlatformRole::SuperAdmin && $user->is_super_admin && !$user->platform_role);
                                                            ?>
                                                            <button
                                                                wire:click="changePlatformRole(<?php echo e($user->id); ?>, '<?php echo e($role->value); ?>')"
                                                                class="flex w-full items-center gap-2 rounded-lg px-3 py-1.5 text-xs transition-colors <?php echo e($isCurrent ? 'bg-[#005F02]/5 text-[#005F02] font-semibold' : 'text-slate-600 hover:bg-slate-100'); ?>"
                                                                <?php echo e($isCurrent ? 'disabled' : ''); ?>

                                                            >
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCurrent): ?>
                                                                    <iconify-icon icon="solar:check-circle-bold" width="13" class="text-[#005F02]"></iconify-icon>
                                                                <?php else: ?>
                                                                    <iconify-icon icon="solar:shield-linear" width="13" class="text-slate-400"></iconify-icon>
                                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                <?php echo e($role->label()); ?>

                                                            </button>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                    </div>

                                                    
                                                    <div class="pt-2 mt-2 border-t border-slate-200">
                                                        <button
                                                            @click="$dispatch('confirm-action', { title: 'Révoquer', message: 'Révoquer l\u0027accès plateforme de cet utilisateur ?', confirmLabel: 'Révoquer', variant: 'danger', onConfirm: () => $wire.revokePlatformRole(<?php echo e($user->id); ?>) })"
                                                            class="flex w-full items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-xs font-medium text-red-600 bg-white border border-slate-200 hover:bg-red-50 hover:border-red-200 transition-colors"
                                                        >
                                                            <iconify-icon icon="solar:shield-cross-bold-duotone" width="16" class="text-red-500"></iconify-icon>
                                                            <?php echo e(__('platform_invitations.revoke')); ?>

                                                        </button>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <iconify-icon icon="solar:users-group-rounded-bold-duotone" width="44" class="mb-3 text-slate-300"></iconify-icon>
                                    <p class="text-sm font-medium"><?php echo e(__('super_admin.users.empty')); ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($users->hasPages()): ?>
            <div class="border-t border-slate-100 px-5 py-3">
                <?php echo e($users->links()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div x-data="{ open: $wire.$entangle('showInviteModal') }" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div
            x-show="open"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="open = false"
        ></div>
        <div
            x-show="open"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white rounded-2xl shadow-2xl ring-1 ring-slate-200/60 w-full max-w-md p-6 space-y-4"
            @click.stop
            x-init="$watch('open', v => { if (v) $nextTick(() => $el.querySelector('input[type=email]')?.focus()) })"
        >
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <iconify-icon icon="solar:letter-bold-duotone" width="20" class="text-[#005F02]"></iconify-icon>
                <?php echo e(__('platform_invitations.invite_title')); ?>

            </h3>
            <p class="text-sm text-slate-500"><?php echo e(__('platform_invitations.invite_desc')); ?></p>

            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1"><?php echo e(__('platform_invitations.email_label')); ?></label>
                    <input
                        type="email"
                        wire:model="inviteEmail"
                        placeholder="<?php echo e(__('platform_invitations.email_placeholder')); ?>"
                        class="w-full rounded-xl border border-slate-200 bg-white py-2.5 px-4 text-sm text-slate-700 placeholder-slate-400 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all"
                    />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['inviteEmail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1"><?php echo e(__('platform_invitations.role_label')); ?></label>
                    <?php if (isset($component)) { $__componentOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfbd96fa9ceb0dd232d7f99b6c6b44c36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select-input','data' => ['options' => $platformInviteRoleOptions,'label' => $platformInviteRoleLabel,'selectedValue' => $inviteRole,'wire:model' => 'inviteRole']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($platformInviteRoleOptions),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($platformInviteRoleLabel),'selected-value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inviteRole),'wire:model' => 'inviteRole']); ?>
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
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button @click="open = false" class="sa-btn-secondary">
                    <?php echo e(__('super_admin.cancel')); ?>

                </button>
                <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['wire:click' => 'sendInvitation','wireTarget' => 'sendInvitation','variant' => 'super']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'sendInvitation','wire-target' => 'sendInvitation','variant' => 'super']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <iconify-icon icon="solar:plain-bold" width="16"></iconify-icon>
                    <?php echo e(__('platform_invitations.send_button')); ?>

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
        </div>
    </div>
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\super-admin\users-global.blade.php ENDPATH**/ ?>