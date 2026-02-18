<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->string('email');
    }

    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));
            return;
        }

        Session::flash('status', __($status));
        $this->redirectRoute('login');
    }
}; ?>

<div class="relative z-10 w-full max-w-[400px] px-4">
    <div class="fade-in shadow-slate-200/50 sm:p-8 bg-white w-full border-slate-100 border rounded-xl p-6 shadow-2xl">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center gap-1.5 mb-3">
                <div class="relative flex items-center justify-center h-8 w-8 rounded-md bg-[#005F02]/5">
                    <iconify-icon icon="solar:restart-square-linear" class="text-[#005F02] text-lg" stroke-width="1.5"></iconify-icon>
                </div>
            </div>
            <h1 class="font-serif text-xl font-medium tracking-tight text-[#002e01]">Réinitialiser</h1>
            <p class="mt-1 text-xs text-slate-500">Créez votre nouveau mot de passe sécurisé.</p>
        </div>

        <!-- Form -->
        <form wire:submit="resetPassword" class="space-y-4">
            
            <div class="space-y-1.5">
                <label for="email" class="block text-[11px] font-medium text-slate-700">Adresse e-mail</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <iconify-icon icon="solar:letter-linear" class="text-slate-400 text-sm" stroke-width="1.5"></iconify-icon>
                    </div>
                    <input wire:model="email" id="email" type="email" required autofocus class="block w-full rounded-md border-0 bg-slate-50 py-2 pl-9 pr-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[#005F02] text-sm transition-all duration-200">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div class="space-y-1.5">
                <label for="password" class="block text-[11px] font-medium text-slate-700">Nouveau mot de passe</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <iconify-icon icon="solar:lock-password-linear" class="text-slate-400 text-sm" stroke-width="1.5"></iconify-icon>
                    </div>
                    <input wire:model="password" id="password" type="password" required autocomplete="new-password" class="block w-full rounded-md border-0 bg-slate-50 py-2 pl-9 pr-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[#005F02] text-sm transition-all duration-200">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-[11px] font-medium text-slate-700">Confirmation</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <iconify-icon icon="solar:check-circle-linear" class="text-slate-400 text-sm" stroke-width="1.5"></iconify-icon>
                    </div>
                    <input wire:model="password_confirmation" id="password_confirmation" type="password" required autocomplete="new-password" class="block w-full rounded-md border-0 bg-slate-50 py-2 pl-9 pr-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[#005F02] text-sm transition-all duration-200">
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>

            <div class="pt-2">
                <button type="submit" class="group flex w-full items-center justify-center gap-2 rounded-lg bg-[#005F02] px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-[#004d02] hover:shadow-lg hover:shadow-[#005F02]/20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#005F02] transition-all duration-200 active:scale-[0.98]">
                    Réinitialiser le mot de passe
                    <iconify-icon icon="solar:arrow-right-linear" class="text-white text-sm transition-transform group-hover:translate-x-1" stroke-width="1.5"></iconify-icon>
                </button>
            </div>
        </form>
    </div>
</div>