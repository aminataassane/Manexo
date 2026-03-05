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
        <button wire:click="disable" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
            {{ __('Désactiver 2FA') }}
        </button>
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
                {!! QrCode::size(200)->generate($qrCodeUrl) !!}
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
                wire:model="code"
                maxlength="6"
                inputmode="numeric"
                autocomplete="one-time-code"
                class="w-full px-3 py-2 border rounded-lg mb-2"
                placeholder="000000"
            >
            @error('code') <p class="text-red-600 text-sm mb-2">{{ $message }}</p> @enderror

            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                {{ __('Vérifier et activer') }}
            </button>
        </form>
    @endif
</div>
