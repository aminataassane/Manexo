<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }

        $this->reset('email');
        session()->flash('status', __($status));
    }
}; ?>

<div class="relative z-10 w-full max-w-[400px] px-4">
    <div class="fade-in shadow-slate-200/50 sm:p-8 bg-white w-full border-slate-100 border rounded-xl p-6 shadow-2xl">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center gap-1.5 mb-3">
                <div class="relative flex items-center justify-center h-8 w-8 rounded-md bg-[#005F02]/5">
                    <iconify-icon icon="solar:shield-keyhole-linear" class="text-[#005F02] text-lg" stroke-width="1.5"></iconify-icon>
                </div>
            </div>
            <h1 class="font-serif text-xl font-medium tracking-tight text-[#002e01]">Mot de passe oublié ?</h1>
            <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                Aucun problème. Indiquez-nous votre email et nous vous enverrons un lien de réinitialisation.
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Form -->
        <form wire:submit="sendPasswordResetLink" class="space-y-4">
            
            <div class="space-y-1.5">
                <label for="email" class="block text-[11px] font-medium text-slate-700">Adresse e-mail</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <iconify-icon icon="solar:letter-linear" class="text-slate-400 text-sm" stroke-width="1.5"></iconify-icon>
                    </div>
                    <input wire:model="email" id="email" type="email" name="email" required autofocus placeholder="exemple@entreprise.com" class="block w-full rounded-md border-0 bg-slate-50 py-2 pl-9 pr-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[#005F02] text-sm transition-all duration-200">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div class="pt-2">
                <x-manexo.action-button type="submit" wire-target="sendPasswordResetLink" variant="primary" class="group !w-full !rounded-lg !bg-[#005F02] hover:!bg-[#004d02] px-4 py-2 text-sm font-medium shadow-sm hover:shadow-lg hover:shadow-[#005F02]/20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#005F02] transition-all duration-200 active:scale-[0.98]" :loading-label="__('ui.action.sending')">
                    Envoyer le lien
                    <iconify-icon icon="solar:plain-linear" class="text-white text-sm transition-transform group-hover:translate-x-1" stroke-width="1.5"></iconify-icon>
                </x-manexo.action-button>
            </div>
        </form>

        <div class="mt-5 text-center pt-4 border-t border-slate-50">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 hover:text-[#005F02] transition-colors">
                <iconify-icon icon="solar:arrow-left-linear"></iconify-icon>
                Retour à la connexion
            </a>
        </div>
    </div>
</div>