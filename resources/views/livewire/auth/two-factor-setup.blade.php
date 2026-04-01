<div class="max-w-lg mx-auto py-10 px-4">
    <h2 class="text-2xl font-bold mb-6">{{ __('Authentification à deux facteurs (2FA)') }}</h2>

    @if (session('2fa_required'))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <p class="text-yellow-800">{{ __('Votre administrateur exige la configuration de l\'authentification à deux facteurs.') }}</p>
        </div>
    @endif

    @if ($confirmed && !$showRecoveryCodes)
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-green-800 font-medium">{{ __('L\'authentification à deux facteurs est activée.') }}</p>
        </div>
        <form wire:submit="disable" class="mt-4 space-y-3">
            <label for="disablePassword" class="block text-sm font-medium text-gray-700">
                {{ __('Confirmez votre mot de passe pour désactiver') }}
            </label>
            <input
                type="password"
                id="disablePassword"
                wire:model.defer="disablePassword"
                wire:loading.attr="disabled"
                wire:target="disable"
                class="w-full px-3 py-2 border rounded-lg"
                placeholder="{{ __('Mot de passe') }}"
                autocomplete="current-password"
            >
            @error('disablePassword') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            <x-manexo.action-button type="submit" wire-target="disable" variant="danger-solid" class="!rounded-lg">
                {{ __('Désactiver 2FA') }}
            </x-manexo.action-button>
        </form>
    @elseif ($showRecoveryCodes)
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-green-800 font-medium mb-2">{{ __('2FA activée avec succès !') }}</p>
            <p class="text-sm text-green-700">{{ __('Conservez ces codes de récupération en lieu sûr. Chaque code ne peut être utilisé qu\'une seule fois.') }}</p>
        </div>
        <div class="bg-gray-100 rounded-lg p-4 font-mono text-sm mb-6">
            @foreach ($recoveryCodes as $code)
                <div class="py-1">{{ $code }}</div>
            @endforeach
        </div>
        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 inline-block">
            {{ __('Continuer') }}
        </a>
    @else
        <div class="mb-6">
            <p class="text-gray-700 mb-4">{{ __('Scannez le QR code ci-dessous avec votre application d\'authentification (Google Authenticator, Authy, etc.)') }}</p>

            <div class="flex justify-center mb-4">
                <img
                    src="https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl={{ urlencode($qrCodeUrl) }}"
                    alt="{{ __('QR code') }}"
                    width="200"
                    height="200"
                    class="mx-auto"
                    loading="eager"
                    decoding="async"
                />
            </div>

            <div class="text-center text-sm text-gray-500 mb-4">
                {{ __('Ou entrez cette clé manuellement :') }}
                <code class="block mt-1 text-base font-mono bg-gray-100 px-3 py-1 rounded">{{ $secret }}</code>
            </div>
        </div>

        <form wire:submit="confirm">
            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Code de vérification') }}
            </label>
            <input
                type="text"
                id="code"
                wire:model.defer="code"
                wire:loading.attr="disabled"
                wire:target="confirm"
                maxlength="6"
                inputmode="numeric"
                autocomplete="one-time-code"
                class="w-full px-3 py-2 border rounded-lg mb-2"
                placeholder="000000"
            >
            @error('code') <p class="text-red-600 text-sm mb-2">{{ $message }}</p> @enderror

            <x-manexo.action-button type="submit" wire-target="confirm" variant="primary" class="!w-full !rounded-lg !bg-blue-600 hover:!bg-blue-700" :loading-label="__('ui.action.loading')">
                {{ __('Vérifier et activer') }}
            </x-manexo.action-button>
        </form>
    @endif
</div>
