<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use PragmaRX\Google2FA\Google2FA;

#[Layout('layouts.manexo-app')]
#[Title('Configuration 2FA')]
class TwoFactorSetup extends Component
{
    public string $code = '';
    public string $qrCodeUrl = '';
    public string $secret = '';
    public array $recoveryCodes = [];
    public bool $showRecoveryCodes = false;
    public bool $confirmed = false;

    public function mount(): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);

        // If already configured, show management view
        if ($user->two_factor_confirmed_at) {
            $this->confirmed = true;
            return;
        }

        $this->generateSecret();
    }

    private function generateSecret(): void
    {
        $user = Auth::user();
        $google2fa = new Google2FA();

        $this->secret = $google2fa->generateSecretKey();
        $this->qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name', 'Manexo'),
            $user->email,
            $this->secret,
        );
    }

    public function confirm(): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);

        $this->validate([
            'code' => 'required|string|digits:6',
        ]);

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($this->secret, $this->code);

        if (! $valid) {
            $this->addError('code', __('Code invalide. Veuillez réessayer.'));
            return;
        }

        // Generate recovery codes
        $recoveryCodes = Collection::times(8, fn () => Str::random(10))->all();

        $user->forceFill([
            'two_factor_secret' => Crypt::encryptString($this->secret),
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode($recoveryCodes)),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->recoveryCodes = $recoveryCodes;
        $this->showRecoveryCodes = true;
        $this->confirmed = true;

        session()->forget('2fa_pending');
    }

    public function disable(): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->confirmed = false;
        $this->showRecoveryCodes = false;
        $this->generateSecret();
    }

    public function render()
    {
        return view('livewire.auth.two-factor-setup');
    }
}
