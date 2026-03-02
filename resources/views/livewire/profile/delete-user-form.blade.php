<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/');
    }
}; ?>

<section>
    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('pages.profile.delete_my_account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('pages.profile.confirm_delete_title') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('pages.profile.confirm_delete_text') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('pages.profile.password') }}" class="sr-only" />

                <x-text-input
                    wire:model="password"
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('pages.profile.password') }}"
                />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('pages.profile.cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('pages.profile.delete_my_account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
