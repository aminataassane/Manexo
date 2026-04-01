<div class="z-10 w-full h-full max-w-5xl relative overflow-y-auto custom-scrollbar" style="--accent: <?php echo e($primary_color ?: '#005F02'); ?>;">
    <!-- Wrapper for centering and scrolling -->
    <div class="flex flex-col items-center justify-center min-h-full w-full px-6 py-12">

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSwitching): ?>
            <!-- MODE: SWITCH ORGANIZATION -->
            <div class="w-full max-w-2xl fade-in">
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl mb-4 bg-white shadow-sm ring-1 ring-slate-200 text-[var(--accent)]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path><rect width="20" height="14" x="2" y="6" rx="2"></rect></svg>
                    </div>
                    <h1 class="font-serif text-3xl font-medium tracking-tight text-slate-900 mb-2">Changer d'espace de travail</h1>
                    <p class="text-sm text-slate-500">Sélectionnez l'entreprise avec laquelle vous souhaitez travailler.</p>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingInvitations->isNotEmpty()): ?>
                    <div class="mb-6 w-full">
                        <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-600"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            <?php echo e(__('invitations.pending_title')); ?>

                        </h3>
                        <div class="space-y-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pendingInvitations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <?php
                                    $roleLabel = match ($inv->role) {
                                        'owner' => 'Propriétaire',
                                        'admin' => 'Admin',
                                        'agent' => 'Agent',
                                        default => 'Membre',
                                    };
                                    $invInitial = mb_strtoupper(mb_substr((string) ($inv->organization?->name ?? '?'), 0, 1));
                                    $daysLeft = (int) now()->diffInDays($inv->expires_at, false);
                                ?>
                                <div class="flex items-center gap-4 rounded-2xl border border-cyan-200 bg-cyan-50/30 p-4">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-cyan-100 font-serif font-bold text-cyan-700 text-sm">
                                        <?php echo e($invInitial); ?>

                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="font-semibold text-slate-900 truncate"><?php echo e($inv->organization?->name ?? '—'); ?></div>
                                        <div class="text-[11px] text-slate-500 flex items-center gap-2 mt-0.5">
                                            <span class="inline-flex items-center rounded-full bg-cyan-100 px-2 py-0.5 text-[10px] font-medium text-cyan-700"><?php echo e($roleLabel); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inv->inviter): ?>
                                                <span><?php echo e(__('invitations.invited_by', ['name' => $inv->inviter->name])); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <span class="text-slate-400">·</span>
                                            <span><?php echo e(__('invitations.expires_in', ['days' => $daysLeft])); ?></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <button type="button" wire:click="declineInvitation(<?php echo e($inv->id); ?>)" class="rounded-lg px-3 py-1.5 text-xs font-medium text-slate-600 bg-white border border-slate-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors">
                                            <?php echo e(__('invitations.decline_button')); ?>

                                        </button>
                                        <button type="button" wire:click="acceptInvitation(<?php echo e($inv->id); ?>)" class="rounded-lg px-3 py-1.5 text-xs font-medium text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-sm">
                                            <?php echo e(__('invitations.accept_button')); ?>

                                        </button>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="grid gap-4 sm:grid-cols-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $organizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $org): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php
                            $role = $org->pivot?->role ?? 'member';
                            $roleLabel = match ($role) {
                                'owner' => 'Propriétaire',
                                'admin' => 'Admin',
                                'agent' => 'Agent',
                                default => 'Membre',
                            };
                            $initial = mb_strtoupper(mb_substr((string) $org->name, 0, 1));
                            $isActive = session('current_organization_id') == $org->id;
                        ?>

                        <button
                            type="button"
                            wire:click="selectOrganization(<?php echo e($org->id); ?>)"
                            class="group relative flex items-center gap-4 rounded-2xl border p-4 text-left transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5 <?php echo e($isActive ? 'border-[var(--accent)] bg-[var(--accent-soft)]/10 ring-1 ring-[var(--accent)]' : 'border-slate-200 bg-white hover:border-[var(--accent-soft)]'); ?>"
                        >
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-lg font-bold shadow-sm transition-colors"
                                 style="background: <?php echo e($org->primary_color ? 'color-mix(in srgb, '.$org->primary_color.' 15%, white)' : '#F3F4F6'); ?>; color: <?php echo e($org->primary_color ?: '#4B5563'); ?>;">
                                <?php echo e($initial); ?>

                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-slate-900 truncate group-hover:text-[var(--accent)] transition-colors"><?php echo e($org->name); ?></div>
                                <div class="text-xs text-slate-500 flex items-center gap-1.5 mt-0.5">
                                    <span class="inline-block w-1.5 h-1.5 rounded-full <?php echo e($isActive ? 'bg-emerald-500' : 'bg-slate-300'); ?>"></span>
                                    <?php echo e($roleLabel); ?>

                                </div>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isActive): ?>
                                <div class="absolute top-4 right-4 text-[var(--accent)]">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="col-span-2 rounded-xl border border-dashed border-slate-300 p-8 text-center">
                            <p class="text-sm text-slate-500">Aucune autre entreprise disponible.</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <!-- Create New Card -->
                    <button type="button" wire:click="$set('isSwitching', false)" class="group flex items-center justify-center gap-3 rounded-2xl border border-dashed border-slate-300 bg-slate-50/50 p-4 text-slate-500 transition-all hover:border-[var(--accent)] hover:bg-[var(--accent-soft)]/5 hover:text-[var(--accent)]">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-slate-200 group-hover:ring-[var(--accent)]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                        </div>
                        <span class="font-medium text-sm">Créer une nouvelle entreprise</span>
                    </button>
                </div>

                <div class="mt-10 text-center">
                    <a href="<?php echo e(route('dashboard')); ?>" class="text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors inline-flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                        Retour au tableau de bord
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- MODE: WELCOME / SETUP (Original Design) -->
            <div class="text-center mb-10 fade-in">
                <div class="inline-flex items-center justify-center h-10 w-10 rounded-xl mb-4" style="background-color: color-mix(in srgb, var(--accent) 8%, transparent); color: var(--accent);">
                    <!-- Lucide Icon: Globe -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path><path d="M2 12h20"></path></svg>
                </div>
                <h1 class="font-serif text-3xl font-medium tracking-tight text-[#002e01] mb-2">Bienvenue sur MANEXO</h1>
                <p class="text-sm text-slate-500 max-w-md mx-auto">Pour continuer, créez votre propre structure ou rejoignez un espace de travail existant.</p>
            </div>

            <!-- Two Column Layout -->
            <div class="grid md:grid-cols-2 gap-6 w-full max-w-4xl items-start">

                <!-- 1. Create Enterprise Block (Primary Action) -->
                <div class="fade-in fade-in-delay-1 group relative flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/50 hover:shadow-2xl hover:shadow-slate-200/60 transition-all duration-300 hover:-translate-y-1">
                    <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity" style="color: var(--accent);">
                        <!-- Lucide Icon: Plus Square -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"></rect><path d="M8 12h8"></path><path d="M12 8v8"></path></svg>
                    </div>

                    <div class="mb-5 relative z-10">
                        <h2 class="text-base font-medium text-slate-900 flex items-center gap-2">
                            <!-- Lucide Icon: Plus Circle -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--accent);"><circle cx="12" cy="12" r="10"></circle><path d="M8 12h8"></path><path d="M12 8v8"></path></svg>
                            Créer une entreprise
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-1">Configurez votre nouvel espace de travail.</p>
                    </div>

                    <form wire:submit.prevent="createOrganization" class="space-y-4 flex-1 flex flex-col relative z-10">
                        <div class="space-y-1.5">
                            <label for="company-name" class="block text-[11px] font-medium text-slate-700">Nom de la structure</label>
                            <input
                                type="text"
                                id="company-name"
                                wire:model.defer="name"
                                placeholder="Ex: Mon Agence Créative"
                                class="block w-full rounded-lg border-0 bg-slate-50 py-2.5 px-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset text-sm transition-all"
                                style="--tw-ring-color: var(--accent);"
                            >
                            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('name')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('name'))]); ?>
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

                        <div class="space-y-2">
                            <label class="block text-[11px] font-medium text-slate-700">Couleur de l'espace</label>
                            <div class="flex items-center gap-3">
                                <?php
                                    $colorOptions = [
                                        ['id' => 'c1', 'value' => '#005F02', 'swatch' => 'bg-[#005F02]'],
                                        ['id' => 'c2', 'value' => '#2563EB', 'swatch' => 'bg-blue-600'],
                                        ['id' => 'c3', 'value' => '#7C3AED', 'swatch' => 'bg-violet-600'],
                                        ['id' => 'c4', 'value' => '#F97316', 'swatch' => 'bg-orange-500'],
                                    ];
                                ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $colorOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <div class="relative">
                                        <input
                                            type="radio"
                                            name="color"
                                            id="<?php echo e($opt['id']); ?>"
                                            class="color-radio peer sr-only"
                                            wire:model="primary_color"
                                            value="<?php echo e($opt['value']); ?>"
                                        >
                                        <label
                                            for="<?php echo e($opt['id']); ?>"
                                            class="cursor-pointer block h-6 w-6 rounded-full <?php echo e($opt['swatch']); ?> hover:opacity-90 transition-transform ring-2 ring-transparent ring-offset-2 ring-offset-white"
                                        ></label>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>

                            <div class="mt-3 grid grid-cols-[auto,1fr] gap-3 items-center">
                                <input
                                    type="color"
                                    class="h-9 w-10 rounded-md border border-slate-200 bg-white p-1"
                                    wire:model.live="primary_color"
                                    aria-label="Choisir une couleur"
                                >
                                <div class="space-y-1">
                                    <label for="primary-color" class="block text-[11px] font-medium text-slate-700">Code couleur (hex)</label>
                                    <input
                                        id="primary-color"
                                        type="text"
                                        inputmode="text"
                                        autocomplete="off"
                                        placeholder="#005F02"
                                        class="block w-full rounded-lg border-0 bg-slate-50 py-2 px-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset text-sm transition-all"
                                        style="--tw-ring-color: var(--accent);"
                                        wire:model.debounce.250ms="primary_color"
                                    >
                                    <p class="text-[10px] text-slate-500">Ex: <span class="font-mono">#005F02</span> ou <span class="font-mono">#2563EB</span></p>
                                </div>
                            </div>

                            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('primary_color')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('primary_color'))]); ?>
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

                        <div class="pt-4 mt-auto">
                            <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','wireTarget' => 'createOrganization','variant' => 'primary','class' => 'group/btn !w-full !shadow-md active:scale-[0.98]','style' => 'background-color: var(--accent); box-shadow: 0 10px 25px rgba(0,0,0,0.08);','loadingLabel' => __('ui.action.loading')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire-target' => 'createOrganization','variant' => 'primary','class' => 'group/btn !w-full !shadow-md active:scale-[0.98]','style' => 'background-color: var(--accent); box-shadow: 0 10px 25px rgba(0,0,0,0.08);','loading-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.action.loading'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                Créer et continuer
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="transition-transform group-hover/btn:translate-x-1"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
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

                <!-- 2. Select / Join Block (Secondary Action) -->
                <div class="fade-in fade-in-delay-2 flex flex-col rounded-2xl border border-slate-200 bg-slate-50/50 backdrop-blur-sm p-6 hover:bg-white transition-colors duration-300 h-full">

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingInvitations->isNotEmpty()): ?>
                        <div class="mb-5">
                            <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-cyan-600"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                <?php echo e(__('invitations.pending_title')); ?>

                            </h3>
                            <div class="space-y-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pendingInvitations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <?php
                                        $invRoleLabel = match ($inv->role) {
                                            'owner' => 'Propriétaire',
                                            'admin' => 'Admin',
                                            'agent' => 'Agent',
                                            default => 'Membre',
                                        };
                                        $invInitial = mb_strtoupper(mb_substr((string) ($inv->organization?->name ?? '?'), 0, 1));
                                        $daysLeft = (int) now()->diffInDays($inv->expires_at, false);
                                    ?>
                                    <div class="rounded-xl border border-cyan-200 bg-cyan-50/40 p-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-cyan-100 font-serif font-medium text-sm text-cyan-700">
                                                <?php echo e($invInitial); ?>

                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-medium text-slate-900 truncate"><?php echo e($inv->organization?->name ?? '—'); ?></div>
                                                <div class="text-[10px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                                                    <span class="inline-flex items-center rounded-full bg-cyan-100 px-1.5 py-0.5 text-[9px] font-medium text-cyan-700"><?php echo e($invRoleLabel); ?></span>
                                                    <span><?php echo e(__('invitations.expires_in', ['days' => $daysLeft])); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 mt-2.5">
                                            <button type="button" wire:click="declineInvitation(<?php echo e($inv->id); ?>)" class="flex-1 rounded-lg py-1.5 text-[11px] font-medium text-slate-600 bg-white border border-slate-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors text-center">
                                                <?php echo e(__('invitations.decline_button')); ?>

                                            </button>
                                            <button type="button" wire:click="acceptInvitation(<?php echo e($inv->id); ?>)" class="flex-1 rounded-lg py-1.5 text-[11px] font-medium text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-sm text-center">
                                                <?php echo e(__('invitations.accept_button')); ?>

                                            </button>
                                        </div>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                        <div class="border-t border-slate-200/60 mb-5"></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="mb-5 flex items-start justify-between">
                        <div>
                            <h2 class="text-base font-medium text-slate-800 flex items-center gap-2">
                                <!-- Lucide Icon: Building 2 -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-slate-600"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path><path d="M10 6h4"></path><path d="M10 10h4"></path><path d="M10 14h4"></path><path d="M10 18h4"></path></svg>
                                Vos entreprises
                            </h2>
                            <p class="text-[11px] text-slate-500 mt-1">Sélectionnez un espace existant.</p>
                        </div>
                        <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-1 text-[10px] font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">
                            <?php echo e($organizations->count()); ?> disponible<?php echo e($organizations->count() > 1 ? 's' : ''); ?>

                        </span>
                    </div>

                    <!-- List of existing companies -->
                    <div class="space-y-2 flex-1 overflow-y-auto custom-scrollbar max-h-[180px] pr-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $organizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $org): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <?php
                                $role = $org->pivot?->role ?? 'member';
                                $roleLabel = match ($role) {
                                    'owner' => 'Propriétaire',
                                    'admin' => 'Admin',
                                    'agent' => 'Agent',
                                    default => 'Membre',
                                };
                                $initial = mb_strtoupper(mb_substr((string) $org->name, 0, 1));
                            ?>

                            <button
                                type="button"
                                wire:click="selectOrganization(<?php echo e($org->id); ?>)"
                                class="w-full group flex items-center justify-between rounded-xl border border-slate-200 bg-white p-3 shadow-sm transition-all duration-200 text-left"
                                style="--tw-ring-color: var(--accent);"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#F2E3BB] font-serif font-medium text-sm" style="color: var(--accent);">
                                        <?php echo e($initial); ?>

                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-slate-900 transition-colors group-hover:text-[color:var(--accent)]"><?php echo e($org->name); ?></div>
                                        <div class="text-[10px] text-slate-500"><?php echo e($roleLabel); ?></div>
                                    </div>
                                </div>
                                <!-- Lucide Icon: Log In -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-slate-300 transition-colors group-hover:text-[color:var(--accent)]"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" x2="3" y1="12" y2="12"></line></svg>
                            </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="rounded-xl border border-dashed border-slate-200 bg-white/60 p-4">
                                <div class="text-sm font-medium text-slate-700">Aucune entreprise pour le moment</div>
                                <div class="text-[11px] text-slate-500 mt-1">Créez votre première structure à gauche pour continuer.</div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Join Action Footer -->
                    <div class="pt-4 mt-auto border-t border-slate-200/60">
                        <button
                            type="button"
                            wire:click="openInviteModal"
                            class="w-full text-center text-xs font-medium text-slate-500 transition-colors flex items-center justify-center gap-1.5 py-1 hover:text-[color:var(--accent)]"
                        >
                            <!-- Lucide Icon: Key -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="7.5" cy="15.5" r="5.5"></circle><path d="m21 2-9.6 9.6"></path><path d="m15.5 7.5 3 3L22 7l-3-3"></path></svg>
                            Rejoindre avec un code d'invitation
                        </button>
                    </div>
                </div>
            </div>

            <!-- Logout / Cancel -->
            <div class="mt-12 fade-in fade-in-delay-2">
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="text-[11px] font-medium text-slate-400 hover:text-slate-600 transition-colors flex items-center gap-1.5">
                        <!-- Lucide Icon: Log Out -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" x2="9" y1="12" y2="12"></line></svg>
                        Se déconnecter
                    </button>
                </form>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    </div>

    <!-- Invite Modal -->
    <div x-data="{ open: $wire.$entangle('showInviteModal') }" x-show="open" x-cloak @keydown.escape.window="open && (open = false)" class="fixed inset-0 z-50" style="display:none;">
    <div
        x-show="open"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="$wire.closeInviteModal()"
    ></div>

    <div class="relative mx-auto flex min-h-full max-w-lg items-center justify-center px-6">
        <div
            x-show="open"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="w-full rounded-2xl border border-white/30 bg-white/95 p-6 shadow-2xl ring-1 ring-black/5"
            @click.stop
        >
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Rejoindre une entreprise</h3>
                    <p class="mt-1 text-[11px] text-slate-500">Entrez le code d'invitation fourni par l'administrateur.</p>
                </div>
                <button type="button" @click="$wire.closeInviteModal()" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors" aria-label="Fermer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>
                </button>
            </div>

            <form wire:submit.prevent="joinWithInviteCode" class="mt-5 space-y-3">
                <div class="space-y-1">
                    <label for="invite_code" class="block text-[11px] font-medium text-slate-700">Code d'invitation</label>
                    <input
                        id="invite_code"
                        type="text"
                        wire:model.defer="invite_code"
                        placeholder="Ex: MANEXO-8F3K2"
                        class="block w-full rounded-lg border-0 bg-slate-50 py-2.5 px-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset text-sm transition-all"
                        style="--tw-ring-color: var(--accent);"
                    >
                    <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('invite_code')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('invite_code'))]); ?>
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

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="$wire.closeInviteModal()" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">
                        Annuler
                    </button>
                    <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','wireTarget' => 'joinWithInviteCode','variant' => 'primary','class' => '!rounded-lg !font-medium','style' => 'background-color: var(--accent);','loadingLabel' => __('ui.action.loading')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire-target' => 'joinWithInviteCode','variant' => 'primary','class' => '!rounded-lg !font-medium','style' => 'background-color: var(--accent);','loading-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.action.loading'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        Rejoindre
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
</div>
</div>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views/livewire/organizations/selector.blade.php ENDPATH**/ ?>