<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use PragmaRX\Google2FA\Google2FA;

#[Layout('layouts.manexo-app')]
#[Title('Vérification 2FA')]
class TwoFactorChallenge extends Component
{
    public string $code = '';
    public string $recoveryCode = '';
    public bool $useRecoveryCode = false;

    public function mount(): void
    {
        $user = Auth::user();

        if (! $user || ! session('2fa_pending')) {
            $this->redirect(route('dashboard'));
            return;
        }
    }

    public function verify(): void
    {
        $user = Auth::user();
        abort_if(! $user, 403);
        abort_if(! session('2fa_pending'), 403);

        if ($this->useRecoveryCode) {
            $this->verifyRecoveryCode($user);
            return;
        }

        $this->validate([
            'code' => 'required|string|digits:6',
        ]);

        if (! $user->two_factor_secret) {
            $this->addError('code', __('2FA non configuré pour ce compte.'));
            return;
        }

        $secret = Crypt::decryptString($user->two_factor_secret);
        $google2fa = new Google2FA();

        if (! $google2fa->verifyKey($secret, $this->code)) {
            $this->addError('code', __('Code invalide. Veuillez réessayer.'));
            return;
        }

        session()->forget('2fa_pending');
        $this->redirect(session()->pull('url.intended', route('dashboard')));
    }

    private function verifyRecoveryCode($user): void
    {
        if (! $user->two_factor_recovery_codes) {
            $this->addError('recoveryCode', __('Code de récupération invalide.'));
            return;
        }

        $this->validate([
            'recoveryCode' => 'required|string|min:5',
        ]);

        $codes = json_decode(Crypt::decryptString($user->two_factor_recovery_codes), true);

        if (! is_array($codes)) {
            $this->addError('recoveryCode', __('Code de récupération invalide.'));
            return;
        }

        $index = collect($codes)->search(fn ($code) => hash_equals((string) $code, $this->recoveryCode));

        if ($index === false) {
            $this->addError('recoveryCode', __('Code de récupération invalide.'));
            return;
        }

        // Remove used recovery code
        unset($codes[$index]);
        $user->forceFill([
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode(array_values($codes))),
        ])->save();

        session()->forget('2fa_pending');
        $this->redirect(session()->pull('url.intended', route('dashboard')));
    }

    public function toggleRecoveryMode(): void
    {
        $this->useRecoveryCode = ! $this->useRecoveryCode;
        $this->code = '';
        $this->recoveryCode = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.auth.two-factor-challenge');
    }
}
