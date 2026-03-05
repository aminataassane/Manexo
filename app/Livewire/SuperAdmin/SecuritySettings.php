<?php

namespace App\Livewire\SuperAdmin;

use App\Models\PlatformSetting;
use App\Services\SuperAdminAuditService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.super-admin', ['title' => 'super_admin.security.title'])]
class SecuritySettings extends Component
{
    // Passwords
    public int $minPasswordLength = 8;
    public bool $requireUppercase = true;
    public bool $requireNumbers = true;
    public bool $requireSpecialChars = false;
    public int $passwordExpirationDays = 0;

    // Sessions & Login
    public int $sessionLifetimeDays = 1;
    public int $maxLoginAttempts = 5;
    public int $lockoutDurationMinutes = 15;
    public int $maxConcurrentSessions = 3;

    // Verification & Security
    public bool $requireEmailVerification = true;
    public bool $force2faForAdmins = false;
    public string $allowedAdminIps = '';

    public function mount(): void
    {
        // Passwords
        $this->minPasswordLength = (int) PlatformSetting::getValue('min_password_length', 8);
        $this->requireUppercase = (bool) PlatformSetting::getValue('require_uppercase', true);
        $this->requireNumbers = (bool) PlatformSetting::getValue('require_numbers', true);
        $this->requireSpecialChars = (bool) PlatformSetting::getValue('require_special_chars', false);
        $this->passwordExpirationDays = (int) PlatformSetting::getValue('password_expiration_days', 0);

        // Sessions & Login
        $this->sessionLifetimeDays = (int) PlatformSetting::getValue('session_lifetime_days', 1);
        $this->maxLoginAttempts = (int) PlatformSetting::getValue('max_login_attempts', 5);
        $this->lockoutDurationMinutes = (int) PlatformSetting::getValue('lockout_duration_minutes', 15);
        $this->maxConcurrentSessions = (int) PlatformSetting::getValue('max_concurrent_sessions', 3);

        // Verification & Security
        $this->requireEmailVerification = (bool) PlatformSetting::getValue('require_email_verification', true);
        $this->force2faForAdmins = (bool) PlatformSetting::getValue('force_2fa_for_admins', false);
        $this->allowedAdminIps = (string) PlatformSetting::getValue('allowed_admin_ips', '');
    }

    public function save(): void
    {
        if (! auth()->user()->canPlatformAdminister()) {
            return;
        }

        $this->validate([
            'minPasswordLength' => 'required|integer|min:6|max:128',
            'requireUppercase' => 'boolean',
            'requireNumbers' => 'boolean',
            'requireSpecialChars' => 'boolean',
            'passwordExpirationDays' => 'required|integer|min:0|max:365',
            'sessionLifetimeDays' => 'required|integer|min:1|max:30',
            'maxLoginAttempts' => 'required|integer|min:1|max:100',
            'lockoutDurationMinutes' => 'required|integer|min:1|max:1440',
            'maxConcurrentSessions' => 'required|integer|min:1|max:20',
            'requireEmailVerification' => 'boolean',
            'force2faForAdmins' => 'boolean',
            'allowedAdminIps' => 'nullable|string|max:1000',
        ]);

        // Passwords
        PlatformSetting::setValue('min_password_length', $this->minPasswordLength, 'integer');
        PlatformSetting::setValue('require_uppercase', $this->requireUppercase ? '1' : '0', 'boolean');
        PlatformSetting::setValue('require_numbers', $this->requireNumbers ? '1' : '0', 'boolean');
        PlatformSetting::setValue('require_special_chars', $this->requireSpecialChars ? '1' : '0', 'boolean');
        PlatformSetting::setValue('password_expiration_days', $this->passwordExpirationDays, 'integer');

        // Sessions & Login
        PlatformSetting::setValue('session_lifetime_days', $this->sessionLifetimeDays, 'integer');
        PlatformSetting::setValue('max_login_attempts', $this->maxLoginAttempts, 'integer');
        PlatformSetting::setValue('lockout_duration_minutes', $this->lockoutDurationMinutes, 'integer');
        PlatformSetting::setValue('max_concurrent_sessions', $this->maxConcurrentSessions, 'integer');

        // Verification & Security
        PlatformSetting::setValue('require_email_verification', $this->requireEmailVerification ? '1' : '0', 'boolean');
        PlatformSetting::setValue('force_2fa_for_admins', $this->force2faForAdmins ? '1' : '0', 'boolean');
        PlatformSetting::setValue('allowed_admin_ips', trim($this->allowedAdminIps), 'string');

        SuperAdminAuditService::log('security.update', null, null, [
            'min_password_length' => $this->minPasswordLength,
            'require_uppercase' => $this->requireUppercase,
            'require_numbers' => $this->requireNumbers,
            'require_special_chars' => $this->requireSpecialChars,
            'password_expiration_days' => $this->passwordExpirationDays,
            'session_lifetime_days' => $this->sessionLifetimeDays,
            'max_login_attempts' => $this->maxLoginAttempts,
            'lockout_duration_minutes' => $this->lockoutDurationMinutes,
            'max_concurrent_sessions' => $this->maxConcurrentSessions,
            'require_email_verification' => $this->requireEmailVerification,
            'force_2fa_for_admins' => $this->force2faForAdmins,
            'allowed_admin_ips' => $this->allowedAdminIps,
        ]);

        session()->flash('success', __('super_admin.security.saved'));
    }

    public function render()
    {
        return view('livewire.super-admin.security-settings');
    }
}
