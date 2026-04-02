<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight"><?php echo e(__('super_admin.profile.title')); ?></h1>
        <p class="text-sm text-slate-500 mt-1"><?php echo e(__('super_admin.profile.subtitle')); ?></p>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('profile_status')): ?>
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm flex items-center gap-3">
            <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
            <?php echo e(session('profile_status')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- LEFT COLUMN -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Personal Info -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-base font-semibold text-slate-900"><?php echo e(__('pages.profile.personal_info_title')); ?></h2>
                    <p class="text-xs text-slate-500 mt-0.5"><?php echo e(__('pages.profile.personal_info_subtitle')); ?></p>
                </div>
                <div class="p-6">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('profile.update-profile-information-form', []);

$key = null;
$__componentSlots = [];

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3399596660-9', $key);

$__html = app('livewire')->mount($__name, $__params, $key, $__componentSlots);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
                </div>
            </div>

            <!-- Organizations -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900"><?php echo e(__('pages.profile.my_companies')); ?></h2>
                        <p class="text-xs text-slate-500 mt-0.5"><?php echo e(__('pages.profile.companies_subtitle')); ?></p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_5 = true; $__currentLoopData = $this->organizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $org): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_5 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <div class="group flex items-center gap-4 rounded-xl border border-slate-200 p-4 transition-all hover:border-[#005F02]/30 hover:bg-emerald-50/30">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-sm font-bold shadow-sm"
                                     style="background: <?php echo e($org->primary_color ? 'color-mix(in srgb, '.$org->primary_color.' 15%, white)' : '#F3F4F6'); ?>; color: <?php echo e($org->primary_color ?: '#4B5563'); ?>;">
                                    <?php echo e(mb_strtoupper(mb_substr($org->name, 0, 1))); ?>

                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-slate-900 truncate"><?php echo e($org->name); ?></p>
                                    <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5">
                                        <iconify-icon icon="solar:shield-user-linear" width="12"></iconify-icon>
                                        <?php echo e(ucfirst((string) $org->pivot->role)); ?>

                                    </p>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_5): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="col-span-2 rounded-xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-500">
                                <?php echo e(__('pages.profile.no_organization')); ?>

                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="space-y-8">
            <!-- Language -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-slate-900 mb-4"><?php echo e(__('pages.profile.language')); ?></h2>
                <div class="grid grid-cols-2 gap-2">
                    <?php $currentLocale = strtoupper((string) app()->getLocale()); ?>
                    <a href="<?php echo e(route('locale.switch', 'fr')); ?>"
                       class="flex items-center justify-center gap-2 rounded-xl border p-2 text-sm font-medium transition-all <?php echo e($currentLocale === 'FR' ? 'border-[#005F02] bg-emerald-50/50 text-[#005F02]' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'); ?>">
                        <span class="text-lg">🇫🇷</span> <?php echo e(__('pages.profile.french')); ?>

                    </a>
                    <a href="<?php echo e(route('locale.switch', 'en')); ?>"
                       class="flex items-center justify-center gap-2 rounded-xl border p-2 text-sm font-medium transition-all <?php echo e($currentLocale === 'EN' ? 'border-[#005F02] bg-emerald-50/50 text-[#005F02]' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'); ?>">
                        <span class="text-lg">🇬🇧</span> <?php echo e(__('pages.profile.english')); ?>

                    </a>
                </div>
            </div>

            <!-- Security -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-slate-900 mb-4"><?php echo e(__('pages.profile.security')); ?></h2>
                <div class="space-y-4">
                    <div class="rounded-xl bg-slate-50 p-4 border border-slate-100">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3"><?php echo e(__('pages.profile.active_sessions')); ?></h3>
                        <div class="space-y-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <?php
                                    $isCurrent = (string) $s->id === (string) $this->currentSessionId;
                                    $dt = \Illuminate\Support\Carbon::createFromTimestamp((int) $s->last_activity);
                                ?>
                                <div class="flex items-start gap-3 text-xs">
                                    <div class="mt-0.5">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCurrent): ?>
                                            <div class="h-2 w-2 rounded-full bg-emerald-500 ring-2 ring-emerald-100"></div>
                                        <?php else: ?>
                                            <div class="h-2 w-2 rounded-full bg-slate-300"></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-slate-900">
                                            <?php echo e($s->ip_address ?? __('pages.profile.unknown_ip')); ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCurrent): ?> <span class="text-emerald-600 ml-1">(<?php echo e(__('pages.profile.current_session')); ?>)</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </p>
                                        <p class="text-slate-500 truncate"><?php echo e($s->user_agent); ?></p>
                                        <p class="text-slate-400 mt-0.5"><?php echo e($dt->diffForHumans()); ?></p>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>

                        <form method="POST" action="<?php echo e(route('profile.sessions.logout_all')); ?>" class="mt-4 pt-4 border-t border-slate-200">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full rounded-lg bg-white border border-slate-200 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm">
                                <?php echo e(__('pages.profile.logout_other_sessions')); ?>

                            </button>
                        </form>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3"><?php echo e(__('pages.profile.password')); ?></h3>
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('profile.update-password-form', []);

$key = null;
$__componentSlots = [];

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3399596660-10', $key);

$__html = app('livewire')->mount($__name, $__params, $key, $__componentSlots);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-red-50 rounded-2xl border border-red-100 p-6">
                <h2 class="text-sm font-semibold text-red-900 mb-2"><?php echo e(__('pages.profile.danger_zone')); ?></h2>
                <p class="text-xs text-red-700 mb-4"><?php echo e(__('pages.profile.danger_zone_text')); ?></p>
                <div x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="text-xs font-bold text-red-600 hover:text-red-800 underline">
                        <?php echo e(__('pages.profile.delete_my_account')); ?>

                    </button>
                    <div x-show="open" x-collapse class="mt-4">
                        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('profile.delete-user-form', []);

$key = null;
$__componentSlots = [];

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3399596660-11', $key);

$__html = app('livewire')->mount($__name, $__params, $key, $__componentSlots);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\super-admin\profile.blade.php ENDPATH**/ ?>