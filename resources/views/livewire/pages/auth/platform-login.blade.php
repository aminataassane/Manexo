<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.platform-guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();

        $user = auth()->user();

        if (! $user?->hasPlatformAccess()) {
            auth()->guard('web')->logout();
            Session::invalidate();
            Session::regenerateToken();

            throw \Illuminate\Validation\ValidationException::withMessages([
                'form.email' => __('platform_login.no_access'),
            ]);
        }

        $this->redirect(route('platform-admin.dashboard', absolute: false));
    }
}; ?>

<div class="relative z-10 w-full min-w-0 max-w-[400px] sm:max-w-[420px] min-[1920px]:max-w-[440px] px-2 sm:px-4">
    <div class="fade-in shadow-indigo-200/40 bg-white/95 backdrop-blur-sm w-full border-indigo-100/60 border rounded-2xl p-5 sm:p-7 lg:p-8 shadow-2xl">

        <!-- Header -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center h-12 w-12 rounded-xl bg-indigo-600 shadow-lg shadow-indigo-200 mb-3">
                <iconify-icon icon="solar:shield-star-bold-duotone" class="text-white text-2xl"></iconify-icon>
            </div>
            <h1 class="font-serif text-xl font-semibold tracking-tight text-slate-900">{{ __('platform_login.title') }}</h1>
            <p class="mt-1 text-xs text-slate-500">{{ __('platform_login.subtitle') }}</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Form -->
        <form wire:submit="login" class="space-y-4">

            <div class="space-y-1.5">
                <label for="email" class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider">{{ __('platform_login.email') }}</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <iconify-icon icon="solar:letter-linear" class="text-slate-400 text-sm"></iconify-icon>
                    </div>
                    <input
                        wire:model="form.email"
                        id="email"
                        type="email"
                        autocomplete="email"
                        placeholder="{{ __('platform_login.email_placeholder') }}"
                        required
                        class="block w-full rounded-xl border-0 bg-slate-50 py-2.5 pl-9 pr-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-500 text-sm transition-all duration-200"
                    >
                </div>
                <x-input-error :messages="$errors->get('form.email')" class="mt-1" />
            </div>

            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider">{{ __('platform_login.password') }}</label>
                </div>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <iconify-icon icon="solar:lock-password-linear" class="text-slate-400 text-sm"></iconify-icon>
                    </div>
                    <input
                        wire:model="form.password"
                        id="password"
                        type="password"
                        required
                        class="block w-full rounded-xl border-0 bg-slate-50 py-2.5 pl-9 pr-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-indigo-500 text-sm transition-all duration-200"
                    >
                </div>
                <x-input-error :messages="$errors->get('form.password')" class="mt-1" />
            </div>

            <div class="flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                <label for="remember" class="ml-2 block text-xs text-slate-500">{{ __('platform_login.remember') }}</label>
            </div>

            <div class="pt-1">
                <x-manexo.action-button type="submit" wire-target="login" variant="primary" class="group !w-full !rounded-xl !bg-indigo-600 hover:!bg-indigo-700 px-4 py-2.5 text-sm font-semibold shadow-sm hover:shadow-lg hover:shadow-indigo-300/30 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all duration-200 active:scale-[0.98]" :loading-label="__('ui.action.loading')">
                    <iconify-icon icon="solar:shield-check-bold" class="text-white/80 text-sm"></iconify-icon>
                    {{ __('platform_login.submit') }}
                    <iconify-icon icon="solar:arrow-right-linear" class="text-white text-sm transition-transform group-hover:translate-x-1"></iconify-icon>
                </x-manexo.action-button>
            </div>
        </form>

        <div class="mt-5 text-center pt-4 border-t border-slate-100">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-[11px] text-slate-500 hover:text-indigo-600 transition-colors">
                <iconify-icon icon="solar:arrow-left-linear" width="12"></iconify-icon>
                {{ __('platform_login.back_to_login') }}
            </a>
        </div>
    </div>
</div>
