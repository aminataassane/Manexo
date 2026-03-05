<?php

namespace App\Livewire\Forms;

use App\Services\PlatformSettingsService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = true;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            RateLimiter::hit($this->throttleKey(), $this->lockoutDurationSeconds());

            throw ValidationException::withMessages([
                'form.email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        // Set 2FA pending flag if user has 2FA enabled
        $user = Auth::user();
        if ($user && $user->two_factor_secret && $user->two_factor_confirmed_at) {
            session(['2fa_pending' => true]);
        }
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), $this->maxLoginAttempts())) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get max login attempts from platform settings.
     */
    protected function maxLoginAttempts(): int
    {
        return (int) app(PlatformSettingsService::class)->get('max_login_attempts', 5);
    }

    /**
     * Get lockout duration in seconds from platform settings.
     */
    protected function lockoutDurationSeconds(): int
    {
        $minutes = (int) app(PlatformSettingsService::class)->get('lockout_duration_minutes', 1);

        return max($minutes, 1) * 60;
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}
