<div class="space-y-8 pb-12">
    <!-- Header -->
    <header>
        <h1 class="text-2xl font-bold text-slate-900">{{ __('super_admin.security.title') }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ __('super_admin.security.subtitle') }}</p>
    </header>

    {{-- Session flash --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-center gap-3 animate-fade-in">
            <iconify-icon icon="solar:check-circle-bold" width="20"></iconify-icon>
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-semibold flex items-center gap-2 mb-2">
                <iconify-icon icon="solar:danger-triangle-bold" width="20"></iconify-icon>
                {{ __('settings.fix_errors') }}
            </p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Info Alert -->
    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 flex items-start gap-3">
        <iconify-icon icon="solar:shield-warning-bold-duotone" width="20" class="text-amber-500 mt-0.5 shrink-0"></iconify-icon>
        <span>{{ __('super_admin.security.info') }}</span>
    </div>

    <form wire:submit="save" class="space-y-8">

        {{-- ═══════════════════════ Section 1 : Mots de passe ═══════════════════════ --}}
        <section class="rounded-2xl border border-slate-200/60 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#F2E3BB]/30">
                    <iconify-icon icon="solar:lock-password-bold-duotone" width="20" class="text-[#005F02]"></iconify-icon>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800">{{ __('super_admin.security.section_passwords') }}</h2>
                    <p class="text-xs text-slate-500">{{ __('super_admin.security.section_passwords_desc') }}</p>
                </div>
            </div>

            <div class="p-6 space-y-6">
                {{-- Min password length --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex-1">
                        <label for="minPasswordLength" class="text-sm font-medium text-slate-700">{{ __('super_admin.security.min_password_length') }}</label>
                        <p class="text-xs text-slate-400 mt-0.5">{{ __('super_admin.security.min_password_length_help') }}</p>
                    </div>
                    <div class="sm:w-32">
                        <input
                            type="text"
                            inputmode="numeric"
                            id="minPasswordLength"
                            wire:model="minPasswordLength"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all text-center @error('minPasswordLength') border-red-300 ring-red-100 @enderror"
                        />
                        @error('minPasswordLength') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <hr class="border-slate-100" />

                {{-- Complexity toggles --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {{-- Uppercase --}}
                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4 cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-[#005F02]/20 has-[:checked]:bg-[#F2E3BB]/20">
                        <div class="relative mt-0.5">
                            <input type="checkbox" wire:model="requireUppercase" class="sr-only peer" />
                            <div class="w-9 h-5 bg-slate-200 rounded-full peer-checked:bg-[#005F02] transition-colors"></div>
                            <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform"></div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-slate-700">{{ __('super_admin.security.require_uppercase') }}</span>
                            <p class="text-xs text-slate-400 mt-0.5">{{ __('super_admin.security.require_uppercase_help') }}</p>
                        </div>
                    </label>

                    {{-- Numbers --}}
                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4 cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-[#005F02]/20 has-[:checked]:bg-[#F2E3BB]/20">
                        <div class="relative mt-0.5">
                            <input type="checkbox" wire:model="requireNumbers" class="sr-only peer" />
                            <div class="w-9 h-5 bg-slate-200 rounded-full peer-checked:bg-[#005F02] transition-colors"></div>
                            <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform"></div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-slate-700">{{ __('super_admin.security.require_numbers') }}</span>
                            <p class="text-xs text-slate-400 mt-0.5">{{ __('super_admin.security.require_numbers_help') }}</p>
                        </div>
                    </label>

                    {{-- Special chars --}}
                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4 cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-[#005F02]/20 has-[:checked]:bg-[#F2E3BB]/20">
                        <div class="relative mt-0.5">
                            <input type="checkbox" wire:model="requireSpecialChars" class="sr-only peer" />
                            <div class="w-9 h-5 bg-slate-200 rounded-full peer-checked:bg-[#005F02] transition-colors"></div>
                            <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform"></div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-slate-700">{{ __('super_admin.security.require_special_chars') }}</span>
                            <p class="text-xs text-slate-400 mt-0.5">{{ __('super_admin.security.require_special_chars_help') }}</p>
                        </div>
                    </label>
                </div>

                <hr class="border-slate-100" />

                {{-- Password expiration --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex-1">
                        <label for="passwordExpirationDays" class="text-sm font-medium text-slate-700">{{ __('super_admin.security.password_expiration') }}</label>
                        <p class="text-xs text-slate-400 mt-0.5">{{ __('super_admin.security.password_expiration_help') }}</p>
                    </div>
                    <div class="sm:w-32">
                        <input
                            type="text"
                            inputmode="numeric"
                            id="passwordExpirationDays"
                            wire:model="passwordExpirationDays"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all text-center @error('passwordExpirationDays') border-red-300 ring-red-100 @enderror"
                        />
                        @error('passwordExpirationDays') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════ Section 2 : Sessions & Connexion ═══════════════════════ --}}
        <section class="rounded-2xl border border-slate-200/60 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-100">
                    <iconify-icon icon="solar:login-3-bold-duotone" width="20" class="text-sky-600"></iconify-icon>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800">{{ __('super_admin.security.section_sessions') }}</h2>
                    <p class="text-xs text-slate-500">{{ __('super_admin.security.section_sessions_desc') }}</p>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Session lifetime --}}
                    <div class="rounded-xl border border-slate-100 p-4 space-y-2">
                        <div class="flex items-center gap-2 mb-1">
                            <iconify-icon icon="solar:clock-circle-bold-duotone" width="16" class="text-slate-400"></iconify-icon>
                            <label for="sessionLifetimeDays" class="text-sm font-medium text-slate-700">{{ __('super_admin.security.session_lifetime') }}</label>
                        </div>
                        <input
                            type="text"
                            inputmode="numeric"
                            id="sessionLifetimeDays"
                            wire:model="sessionLifetimeDays"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all text-center @error('sessionLifetimeDays') border-red-300 ring-red-100 @enderror"
                        />
                        <p class="text-xs text-slate-400">{{ __('super_admin.security.session_lifetime_help') }}</p>
                        @error('sessionLifetimeDays') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Max login attempts --}}
                    <div class="rounded-xl border border-slate-100 p-4 space-y-2">
                        <div class="flex items-center gap-2 mb-1">
                            <iconify-icon icon="solar:shield-cross-bold-duotone" width="16" class="text-slate-400"></iconify-icon>
                            <label for="maxLoginAttempts" class="text-sm font-medium text-slate-700">{{ __('super_admin.security.max_login_attempts') }}</label>
                        </div>
                        <input
                            type="text"
                            inputmode="numeric"
                            id="maxLoginAttempts"
                            wire:model="maxLoginAttempts"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all text-center @error('maxLoginAttempts') border-red-300 ring-red-100 @enderror"
                        />
                        <p class="text-xs text-slate-400">{{ __('super_admin.security.max_login_attempts_help') }}</p>
                        @error('maxLoginAttempts') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Lockout duration --}}
                    <div class="rounded-xl border border-slate-100 p-4 space-y-2">
                        <div class="flex items-center gap-2 mb-1">
                            <iconify-icon icon="solar:lock-bold-duotone" width="16" class="text-slate-400"></iconify-icon>
                            <label for="lockoutDurationMinutes" class="text-sm font-medium text-slate-700">{{ __('super_admin.security.lockout_duration') }}</label>
                        </div>
                        <input
                            type="text"
                            inputmode="numeric"
                            id="lockoutDurationMinutes"
                            wire:model="lockoutDurationMinutes"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all text-center @error('lockoutDurationMinutes') border-red-300 ring-red-100 @enderror"
                        />
                        <p class="text-xs text-slate-400">{{ __('super_admin.security.lockout_duration_help') }}</p>
                        @error('lockoutDurationMinutes') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Max concurrent sessions --}}
                    <div class="rounded-xl border border-slate-100 p-4 space-y-2">
                        <div class="flex items-center gap-2 mb-1">
                            <iconify-icon icon="solar:devices-bold-duotone" width="16" class="text-slate-400"></iconify-icon>
                            <label for="maxConcurrentSessions" class="text-sm font-medium text-slate-700">{{ __('super_admin.security.max_concurrent_sessions') }}</label>
                        </div>
                        <input
                            type="text"
                            inputmode="numeric"
                            id="maxConcurrentSessions"
                            wire:model="maxConcurrentSessions"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all text-center @error('maxConcurrentSessions') border-red-300 ring-red-100 @enderror"
                        />
                        <p class="text-xs text-slate-400">{{ __('super_admin.security.max_concurrent_sessions_help') }}</p>
                        @error('maxConcurrentSessions') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════ Section 3 : Vérification & Avancé ═══════════════════════ --}}
        <section class="rounded-2xl border border-slate-200/60 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-violet-100">
                    <iconify-icon icon="solar:shield-check-bold-duotone" width="20" class="text-violet-600"></iconify-icon>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800">{{ __('super_admin.security.section_verification') }}</h2>
                    <p class="text-xs text-slate-500">{{ __('super_admin.security.section_verification_desc') }}</p>
                </div>
            </div>

            <div class="p-6 space-y-5">
                {{-- Email verification toggle --}}
                <label class="flex items-start gap-4 rounded-xl border border-slate-200 p-4 cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-emerald-200 has-[:checked]:bg-emerald-50/30">
                    <div class="relative mt-0.5 shrink-0">
                        <input type="checkbox" wire:model="requireEmailVerification" class="sr-only peer" />
                        <div class="w-11 h-6 bg-slate-200 rounded-full peer-checked:bg-emerald-500 transition-colors"></div>
                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform"></div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <iconify-icon icon="solar:letter-bold-duotone" width="16" class="text-slate-400"></iconify-icon>
                            <span class="text-sm font-medium text-slate-700">{{ __('super_admin.security.require_email_verification') }}</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5 ml-6">{{ __('super_admin.security.require_email_verification_help') }}</p>
                    </div>
                </label>

                {{-- Force 2FA for admins toggle --}}
                <label class="flex items-start gap-4 rounded-xl border border-slate-200 p-4 cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-violet-200 has-[:checked]:bg-violet-50/30">
                    <div class="relative mt-0.5 shrink-0">
                        <input type="checkbox" wire:model="force2faForAdmins" class="sr-only peer" />
                        <div class="w-11 h-6 bg-slate-200 rounded-full peer-checked:bg-violet-500 transition-colors"></div>
                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform"></div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <iconify-icon icon="solar:shield-keyhole-bold-duotone" width="16" class="text-slate-400"></iconify-icon>
                            <span class="text-sm font-medium text-slate-700">{{ __('super_admin.security.force_2fa_admins') }}</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5 ml-6">{{ __('super_admin.security.force_2fa_admins_help') }}</p>
                    </div>
                </label>

                <hr class="border-slate-100" />

                {{-- IP Restriction --}}
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <iconify-icon icon="solar:global-bold-duotone" width="16" class="text-slate-400"></iconify-icon>
                        <label for="allowedAdminIps" class="text-sm font-medium text-slate-700">{{ __('super_admin.security.allowed_admin_ips') }}</label>
                    </div>
                    <textarea
                        id="allowedAdminIps"
                        wire:model="allowedAdminIps"
                        rows="4"
                        placeholder="{{ __('super_admin.security.allowed_admin_ips_placeholder') }}"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-[#005F02] focus:ring-2 focus:ring-[#005F02]/20 outline-none transition-all font-mono @error('allowedAdminIps') border-red-300 ring-red-100 @enderror"
                    ></textarea>
                    <p class="mt-1 text-xs text-slate-400">{{ __('super_admin.security.allowed_admin_ips_help') }}</p>
                    @error('allowedAdminIps') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        {{-- Submit --}}
        <div class="flex items-center gap-4">
            <x-manexo.action-button type="submit" wire-target="save" variant="super" class="px-8 py-3" :loading-label="__('ui.action.saving')">
                <iconify-icon icon="solar:shield-check-bold" width="18" class="mr-1"></iconify-icon>
                {{ __('super_admin.save') }}
            </x-manexo.action-button>
        </div>
    </form>
</div>
