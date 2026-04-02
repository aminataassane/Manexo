<div class="space-y-8 pb-12">
    <!-- Header -->
    <header>
        <h1 class="text-2xl font-bold text-slate-900"><?php echo e(__('super_admin.security.title')); ?></h1>
        <p class="mt-1 text-sm text-slate-500"><?php echo e(__('super_admin.security.subtitle')); ?></p>
    </header>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-center gap-3 animate-fade-in">
            <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
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

    <!-- Info Alert -->
    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 flex items-start gap-3">
        <iconify-icon icon="solar:shield-warning-bold-duotone" width="20" class="text-amber-500 mt-0.5 shrink-0"></iconify-icon>
        <span><?php echo e(__('super_admin.security.info')); ?></span>
    </div>

    <form wire:submit="save" class="space-y-8">

        
        <section class="rounded-2xl border border-slate-200/60 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#F2E3BB]/30">
                    <iconify-icon icon="solar:lock-password-bold-duotone" width="20" class="text-[#005F02]"></iconify-icon>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800"><?php echo e(__('super_admin.security.section_passwords')); ?></h2>
                    <p class="text-xs text-slate-500"><?php echo e(__('super_admin.security.section_passwords_desc')); ?></p>
                </div>
            </div>

            <div class="p-6 space-y-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex-1">
                        <label for="minPasswordLength" class="text-sm font-medium text-slate-700"><?php echo e(__('super_admin.security.min_password_length')); ?></label>
                        <p class="text-xs text-slate-400 mt-0.5"><?php echo e(__('super_admin.security.min_password_length_help')); ?></p>
                    </div>
                    <div class="sm:w-32">
                        <input
                            type="text"
                            inputmode="numeric"
                            id="minPasswordLength"
                            wire:model="minPasswordLength"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all text-center <?php $__errorArgs = ['minPasswordLength'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 ring-red-100 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['minPasswordLength'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <hr class="border-slate-100" />

                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    
                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4 cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-[#005F02]/20 has-[:checked]:bg-[#F2E3BB]/20">
                        <div class="relative mt-0.5">
                            <input type="checkbox" wire:model="requireUppercase" class="sr-only peer" />
                            <div class="w-9 h-5 bg-slate-200 rounded-full peer-checked:bg-[#005F02] transition-colors"></div>
                            <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform"></div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-slate-700"><?php echo e(__('super_admin.security.require_uppercase')); ?></span>
                            <p class="text-xs text-slate-400 mt-0.5"><?php echo e(__('super_admin.security.require_uppercase_help')); ?></p>
                        </div>
                    </label>

                    
                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4 cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-[#005F02]/20 has-[:checked]:bg-[#F2E3BB]/20">
                        <div class="relative mt-0.5">
                            <input type="checkbox" wire:model="requireNumbers" class="sr-only peer" />
                            <div class="w-9 h-5 bg-slate-200 rounded-full peer-checked:bg-[#005F02] transition-colors"></div>
                            <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform"></div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-slate-700"><?php echo e(__('super_admin.security.require_numbers')); ?></span>
                            <p class="text-xs text-slate-400 mt-0.5"><?php echo e(__('super_admin.security.require_numbers_help')); ?></p>
                        </div>
                    </label>

                    
                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4 cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-[#005F02]/20 has-[:checked]:bg-[#F2E3BB]/20">
                        <div class="relative mt-0.5">
                            <input type="checkbox" wire:model="requireSpecialChars" class="sr-only peer" />
                            <div class="w-9 h-5 bg-slate-200 rounded-full peer-checked:bg-[#005F02] transition-colors"></div>
                            <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform"></div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-slate-700"><?php echo e(__('super_admin.security.require_special_chars')); ?></span>
                            <p class="text-xs text-slate-400 mt-0.5"><?php echo e(__('super_admin.security.require_special_chars_help')); ?></p>
                        </div>
                    </label>
                </div>

                <hr class="border-slate-100" />

                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex-1">
                        <label for="passwordExpirationDays" class="text-sm font-medium text-slate-700"><?php echo e(__('super_admin.security.password_expiration')); ?></label>
                        <p class="text-xs text-slate-400 mt-0.5"><?php echo e(__('super_admin.security.password_expiration_help')); ?></p>
                    </div>
                    <div class="sm:w-32">
                        <input
                            type="text"
                            inputmode="numeric"
                            id="passwordExpirationDays"
                            wire:model="passwordExpirationDays"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all text-center <?php $__errorArgs = ['passwordExpirationDays'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 ring-red-100 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['passwordExpirationDays'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="rounded-2xl border border-slate-200/60 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-100">
                    <iconify-icon icon="solar:login-3-bold-duotone" width="20" class="text-sky-600"></iconify-icon>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800"><?php echo e(__('super_admin.security.section_sessions')); ?></h2>
                    <p class="text-xs text-slate-500"><?php echo e(__('super_admin.security.section_sessions_desc')); ?></p>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    
                    <div class="rounded-xl border border-slate-100 p-4 space-y-2">
                        <div class="flex items-center gap-2 mb-1">
                            <iconify-icon icon="solar:clock-circle-bold-duotone" width="16" class="text-slate-400"></iconify-icon>
                            <label for="sessionLifetimeDays" class="text-sm font-medium text-slate-700"><?php echo e(__('super_admin.security.session_lifetime')); ?></label>
                        </div>
                        <input
                            type="text"
                            inputmode="numeric"
                            id="sessionLifetimeDays"
                            wire:model="sessionLifetimeDays"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all text-center <?php $__errorArgs = ['sessionLifetimeDays'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 ring-red-100 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        />
                        <p class="text-xs text-slate-400"><?php echo e(__('super_admin.security.session_lifetime_help')); ?></p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['sessionLifetimeDays'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="rounded-xl border border-slate-100 p-4 space-y-2">
                        <div class="flex items-center gap-2 mb-1">
                            <iconify-icon icon="solar:shield-cross-bold-duotone" width="16" class="text-slate-400"></iconify-icon>
                            <label for="maxLoginAttempts" class="text-sm font-medium text-slate-700"><?php echo e(__('super_admin.security.max_login_attempts')); ?></label>
                        </div>
                        <input
                            type="text"
                            inputmode="numeric"
                            id="maxLoginAttempts"
                            wire:model="maxLoginAttempts"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all text-center <?php $__errorArgs = ['maxLoginAttempts'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 ring-red-100 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        />
                        <p class="text-xs text-slate-400"><?php echo e(__('super_admin.security.max_login_attempts_help')); ?></p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['maxLoginAttempts'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="rounded-xl border border-slate-100 p-4 space-y-2">
                        <div class="flex items-center gap-2 mb-1">
                            <iconify-icon icon="solar:lock-bold-duotone" width="16" class="text-slate-400"></iconify-icon>
                            <label for="lockoutDurationMinutes" class="text-sm font-medium text-slate-700"><?php echo e(__('super_admin.security.lockout_duration')); ?></label>
                        </div>
                        <input
                            type="text"
                            inputmode="numeric"
                            id="lockoutDurationMinutes"
                            wire:model="lockoutDurationMinutes"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all text-center <?php $__errorArgs = ['lockoutDurationMinutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 ring-red-100 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        />
                        <p class="text-xs text-slate-400"><?php echo e(__('super_admin.security.lockout_duration_help')); ?></p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['lockoutDurationMinutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="rounded-xl border border-slate-100 p-4 space-y-2">
                        <div class="flex items-center gap-2 mb-1">
                            <iconify-icon icon="solar:devices-bold-duotone" width="16" class="text-slate-400"></iconify-icon>
                            <label for="maxConcurrentSessions" class="text-sm font-medium text-slate-700"><?php echo e(__('super_admin.security.max_concurrent_sessions')); ?></label>
                        </div>
                        <input
                            type="text"
                            inputmode="numeric"
                            id="maxConcurrentSessions"
                            wire:model="maxConcurrentSessions"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all text-center <?php $__errorArgs = ['maxConcurrentSessions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 ring-red-100 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        />
                        <p class="text-xs text-slate-400"><?php echo e(__('super_admin.security.max_concurrent_sessions_help')); ?></p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['maxConcurrentSessions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="rounded-2xl border border-slate-200/60 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-violet-100">
                    <iconify-icon icon="solar:shield-check-bold-duotone" width="20" class="text-violet-600"></iconify-icon>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800"><?php echo e(__('super_admin.security.section_verification')); ?></h2>
                    <p class="text-xs text-slate-500"><?php echo e(__('super_admin.security.section_verification_desc')); ?></p>
                </div>
            </div>

            <div class="p-6 space-y-5">
                
                <label class="flex items-start gap-4 rounded-xl border border-slate-200 p-4 cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-emerald-200 has-[:checked]:bg-emerald-50/30">
                    <div class="relative mt-0.5 shrink-0">
                        <input type="checkbox" wire:model="requireEmailVerification" class="sr-only peer" />
                        <div class="w-11 h-6 bg-slate-200 rounded-full peer-checked:bg-emerald-500 transition-colors"></div>
                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform"></div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <iconify-icon icon="solar:letter-bold-duotone" width="16" class="text-slate-400"></iconify-icon>
                            <span class="text-sm font-medium text-slate-700"><?php echo e(__('super_admin.security.require_email_verification')); ?></span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5 ml-6"><?php echo e(__('super_admin.security.require_email_verification_help')); ?></p>
                    </div>
                </label>

                
                <label class="flex items-start gap-4 rounded-xl border border-slate-200 p-4 cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-violet-200 has-[:checked]:bg-violet-50/30">
                    <div class="relative mt-0.5 shrink-0">
                        <input type="checkbox" wire:model="force2faForAdmins" class="sr-only peer" />
                        <div class="w-11 h-6 bg-slate-200 rounded-full peer-checked:bg-violet-500 transition-colors"></div>
                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform"></div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <iconify-icon icon="solar:shield-keyhole-bold-duotone" width="16" class="text-slate-400"></iconify-icon>
                            <span class="text-sm font-medium text-slate-700"><?php echo e(__('super_admin.security.force_2fa_admins')); ?></span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5 ml-6"><?php echo e(__('super_admin.security.force_2fa_admins_help')); ?></p>
                    </div>
                </label>

                <hr class="border-slate-100" />

                
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <iconify-icon icon="solar:global-bold-duotone" width="16" class="text-slate-400"></iconify-icon>
                        <label for="allowedAdminIps" class="text-sm font-medium text-slate-700"><?php echo e(__('super_admin.security.allowed_admin_ips')); ?></label>
                    </div>
                    <textarea
                        id="allowedAdminIps"
                        wire:model="allowedAdminIps"
                        rows="4"
                        placeholder="<?php echo e(__('super_admin.security.allowed_admin_ips_placeholder')); ?>"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all font-mono <?php $__errorArgs = ['allowedAdminIps'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 ring-red-100 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    ></textarea>
                    <p class="mt-1 text-xs text-slate-400"><?php echo e(__('super_admin.security.allowed_admin_ips_help')); ?></p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['allowedAdminIps'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        
        <div class="flex items-center gap-4">
            <?php if (isset($component)) { $__componentOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2828f6ab6d1aa1f13d376de189a6d1c7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.manexo.action-button','data' => ['type' => 'submit','wireTarget' => 'save','variant' => 'super','class' => 'px-8 py-3','loadingLabel' => __('ui.action.saving')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manexo.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','wire-target' => 'save','variant' => 'super','class' => 'px-8 py-3','loading-label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.action.saving'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <iconify-icon icon="solar:shield-check-bold" width="18" class="mr-1"></iconify-icon>
                <?php echo e(__('super_admin.save')); ?>

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
</div><?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\livewire\super-admin\security-settings.blade.php ENDPATH**/ ?>