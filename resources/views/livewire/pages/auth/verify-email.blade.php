<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $code = '';

    /**
     * Verify email using OTP code.
     */
    public function verifyOtp(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        $this->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $otp = $user->getEmailOtpPayload();

        if (! $otp) {
            throw ValidationException::withMessages([
                'code' => "Aucun code actif. Cliquez sur « Renvoyer le code ».",
            ]);
        }

        $maxAttempts = (int) config('auth.email_otp_max_attempts', 5);
        if (($otp['attempts'] ?? 0) >= $maxAttempts) {
            throw ValidationException::withMessages([
                'code' => "Trop de tentatives. Renvoyez un nouveau code.",
            ]);
        }

        if (! ($otp['expires_at'] ?? null) || now()->greaterThan($otp['expires_at'])) {
            throw ValidationException::withMessages([
                'code' => "Code expiré. Renvoyez un nouveau code.",
            ]);
        }

        if (! ($otp['code_hash'] ?? null) || ! Hash::check($this->code, $otp['code_hash'])) {
            $user->incrementEmailOtpAttempts();

            throw ValidationException::withMessages([
                'code' => "Code incorrect.",
            ]);
        }

        $user->markEmailAsVerified();
        $user->clearEmailOtpPayload();

        Session::flash('status', 'email-verified');

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Resend OTP to the user.
     */
    public function resendCode(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        $resendSeconds = (int) config('auth.email_otp_resend_seconds', 60);
        $otp = $user->getEmailOtpPayload();
        $lastSentAt = $otp['last_sent_at'] ?? null;
        if ($lastSentAt && now()->diffInSeconds($lastSentAt) < $resendSeconds) {
            Session::flash('status', 'otp-too-soon');

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'otp-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="relative z-10 w-full max-w-[400px] px-4">
    <div class="fade-in shadow-slate-200/50 sm:p-8 bg-white w-full border-slate-100 border rounded-xl p-6 shadow-2xl">

        <!-- Header -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center gap-1.5 mb-3">
                <div class="relative flex items-center justify-center h-8 w-8 rounded-md bg-[#005F02]/5">
                    <iconify-icon icon="solar:verified-check-linear" class="text-[#005F02] text-lg" stroke-width="1.5"></iconify-icon>
                </div>
            </div>
            <h1 class="font-serif text-xl font-medium tracking-tight text-[#002e01]">Vérifiez votre email</h1>
            <p class="mt-1 text-xs text-slate-500">Saisissez le code à 6 chiffres envoyé à votre adresse e-mail.</p>
        </div>

        @php
            $statusKey = session('status');
            $statusMessage = match ($statusKey) {
                'otp-sent' => 'Code envoyé. Vérifiez votre boîte de réception.',
                'otp-too-soon' => 'Patientez un instant avant de renvoyer un nouveau code.',
                'email-verified' => 'Email vérifié avec succès.',
                default => null,
            };
        @endphp

        @if ($statusMessage)
            <x-auth-session-status class="mb-4" :status="$statusMessage" />
        @endif

        <!-- Form -->
        <form wire:submit="verifyOtp" class="space-y-4">
            <div class="space-y-1.5">
                <label for="code" class="block text-[11px] font-medium text-slate-700">Code OTP</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <iconify-icon icon="solar:password-minimalistic-input-linear" class="text-slate-400 text-sm" stroke-width="1.5"></iconify-icon>
                    </div>
                    <input
                        wire:model="code"
                        id="code"
                        type="text"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        maxlength="6"
                        placeholder="••••••"
                        required
                        class="block w-full rounded-md border-0 bg-slate-50 py-2 pl-9 pr-2.5 tracking-[0.35em] text-center text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-[#005F02] text-sm transition-all duration-200"
                    />
                </div>
                <x-input-error :messages="$errors->get('code')" class="mt-1" />
            </div>

            <div class="pt-2 flex items-center gap-3">
                <button type="submit" class="group flex-1 flex items-center justify-center gap-2 rounded-lg bg-[#005F02] px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-[#004d02] hover:shadow-lg hover:shadow-[#005F02]/20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#005F02] transition-all duration-200 active:scale-[0.98]">
                    Vérifier
                    <iconify-icon icon="solar:arrow-right-linear" class="text-white text-sm transition-transform group-hover:translate-x-1" stroke-width="1.5"></iconify-icon>
                </button>

                <button type="button" wire:click="resendCode" class="text-[11px] font-medium text-slate-500 hover:text-[#005F02] underline underline-offset-4 transition-colors">
                    Renvoyer
                </button>
            </div>
        </form>

        <div class="mt-5 text-center pt-4 border-t border-slate-50">
            <button wire:click="logout" type="button" class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 hover:text-[#005F02] transition-colors">
                <iconify-icon icon="solar:logout-2-linear"></iconify-icon>
                Se déconnecter
            </button>
        </div>
    </div>
</div>
