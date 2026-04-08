<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        $user = Auth::user();

        $user->update([
            'password' => Hash::make($validated['password']),
            'password_changed_at' => now(),
        ]);

        // Invalidate all other sessions to prevent compromised sessions from persisting
        Auth::logoutOtherDevices($validated['password']);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    @php
        $field = 'block w-full rounded-md border-0 bg-slate-50 py-2 px-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[color:var(--accent)] text-sm transition-all duration-200';
    @endphp

    <form wire:submit="updatePassword" class="space-y-5">
        <div>
            <label for="update_password_current_password" class="block text-[11px] font-medium text-slate-700">{{ __('Mot de passe actuel') }}</label>
            <input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" class="mt-1 {{ $field }}" autocomplete="current-password" />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password" class="block text-[11px] font-medium text-slate-700">{{ __('Nouveau mot de passe') }}</label>
            <input wire:model="password" id="update_password_password" name="password" type="password" class="mt-1 {{ $field }}" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-[11px] font-medium text-slate-700">{{ __('Confirmer le mot de passe') }}</label>
            <input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 {{ $field }}" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-wrap items-center gap-4 pt-1">
            <x-manexo.action-button
                type="submit"
                wire-target="updatePassword"
                variant="primary"
                class="!h-10 !rounded-lg !px-5 !font-semibold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#005F02] active:scale-[0.98]"
                style="background-color: var(--accent, #005F02);"
                :loading-label="__('ui.action.saving')"
            >
                {{ __('Mettre à jour') }}
            </x-manexo.action-button>

            <x-action-message class="text-sm text-emerald-600 font-medium" on="password-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
