<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();
        $default = auth()->user()?->is_super_admin
            ? route('platform-admin.dashboard', absolute: false)
            : route('dashboard', absolute: false);
        $this->redirectIntended(default: $default);
    }
}; ?>

<div class="relative z-10 w-full min-w-0 max-w-[400px] sm:max-w-[420px] min-[1920px]:max-w-[440px] px-2 sm:px-4">
    <div class="fade-in shadow-slate-200/50 bg-white w-full border-slate-100 border rounded-xl p-4 sm:p-6 lg:p-8 shadow-2xl">

        <!-- Header -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center gap-1.5 mb-3">
                <div class="relative flex items-center justify-center h-8 w-8 rounded-md bg-[#005F02]/5">
                    <iconify-icon icon="solar:layers-linear" class="text-[#005F02] text-lg" stroke-width="1.5"></iconify-icon>
                </div>
            </div>
            <h1 class="font-serif text-xl font-medium tracking-tight text-[#002e01]">Bon retour</h1>
            <p class="mt-1 text-xs text-slate-500">Connectez-vous pour accéder à votre espace.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Form -->
        <form wire:submit="login" class="space-y-4">

            <div class="space-y-1.5">
                <label for="email" class="block text-[11px] font-medium text-slate-700">Adresse e-mail</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <iconify-icon icon="solar:letter-linear" class="text-slate-400 text-sm" stroke-width="1.5"></iconify-icon>
                    </div>
                    <input wire:model="form.email" id="email" type="email" autocomplete="email" placeholder="exemple@entreprise.com" required class="block w-full rounded-md border-0 bg-slate-50 py-2 pl-9 pr-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[#005F02] text-sm transition-all duration-200">
                </div>
                <x-input-error :messages="$errors->get('form.email')" class="mt-1" />
            </div>

            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-[11px] font-medium text-slate-700">Mot de passe</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-[10px] font-medium text-slate-500 hover:text-[#005F02] transition-colors">Mot de passe oublié ?</a>
                    @endif
                </div>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <iconify-icon icon="solar:lock-password-linear" class="text-slate-400 text-sm" stroke-width="1.5"></iconify-icon>
                    </div>
                    <input wire:model="form.password" id="password" type="password" required class="block w-full rounded-md border-0 bg-slate-50 py-2 pl-9 pr-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[#005F02] text-sm transition-all duration-200">
                </div>
                <x-input-error :messages="$errors->get('form.password')" class="mt-1" />
            </div>

            <div class="flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="h-3 w-3 rounded border-slate-300 text-[#005F02] focus:ring-[#005F02]">
                <label for="remember" class="ml-2 block text-[10px] text-slate-500">Se souvenir de moi</label>
            </div>

            <div class="pt-2">
                <button type="submit" class="group flex w-full items-center justify-center gap-2 rounded-lg bg-[#005F02] px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-[#004d02] hover:shadow-lg hover:shadow-[#005F02]/20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#005F02] transition-all duration-200 active:scale-[0.98]">
                    Se connecter
                    <iconify-icon icon="solar:arrow-right-linear" class="text-white text-sm transition-transform group-hover:translate-x-1" stroke-width="1.5"></iconify-icon>
                </button>
            </div>
        </form>

        <div class="mt-5 text-center pt-4 border-t border-slate-50">
            <p class="text-[11px] text-slate-500">
                Pas encore de compte ?
                <a href="{{ route('register') }}" class="font-normal text-[#005F02] hover:underline hover:text-[#004d02] transition-colors">Créer un compte</a>
            </p>
        </div>
    </div>
    <!-- Footer removed here to avoid duplication with layout -->
</div>
