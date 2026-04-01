<div class="max-w-md mx-auto py-10 px-4">
    <h2 class="text-2xl font-bold mb-6">{{ __('Vérification en deux étapes') }}</h2>

    @if (!$useRecoveryCode)
        <p class="text-gray-700 mb-4">{{ __('Entrez le code à 6 chiffres de votre application d\'authentification.') }}</p>

        <form wire:submit="verify">
            <input
                type="text"
                wire:model.defer="code"
                wire:loading.attr="disabled"
                wire:target="verify"
                maxlength="6"
                inputmode="numeric"
                autocomplete="one-time-code"
                class="w-full px-3 py-2 border rounded-lg mb-2 text-center text-2xl tracking-widest"
                placeholder="000000"
                autofocus
            >
            @error('code') <p class="text-red-600 text-sm mb-2">{{ $message }}</p> @enderror

            <x-manexo.action-button type="submit" wire-target="verify" variant="primary" class="!w-full !rounded-lg !bg-blue-600 hover:!bg-blue-700 mt-2" :loading-label="__('ui.action.loading')">
                {{ __('Vérifier') }}
            </x-manexo.action-button>
        </form>

        <button wire:click="toggleRecoveryMode" wire:loading.attr="disabled" wire:target="toggleRecoveryMode,verify" class="mt-4 text-sm text-blue-600 hover:underline">
            {{ __('Utiliser un code de récupération') }}
        </button>
    @else
        <p class="text-gray-700 mb-4">{{ __('Entrez un de vos codes de récupération.') }}</p>

        <form wire:submit="verify">
            <input
                type="text"
                wire:model.defer="recoveryCode"
                wire:loading.attr="disabled"
                wire:target="verify"
                class="w-full px-3 py-2 border rounded-lg mb-2"
                placeholder="{{ __('Code de récupération') }}"
                autofocus
            >
            @error('recoveryCode') <p class="text-red-600 text-sm mb-2">{{ $message }}</p> @enderror

            <x-manexo.action-button type="submit" wire-target="verify" variant="primary" class="!w-full !rounded-lg !bg-blue-600 hover:!bg-blue-700 mt-2" :loading-label="__('ui.action.loading')">
                {{ __('Vérifier') }}
            </x-manexo.action-button>
        </form>

        <button wire:click="toggleRecoveryMode" wire:loading.attr="disabled" wire:target="toggleRecoveryMode,verify" class="mt-4 text-sm text-blue-600 hover:underline">
            {{ __('Utiliser le code de l\'application') }}
        </button>
    @endif
</div>
