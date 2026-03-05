<?php

namespace App\Rules;

use App\Services\PlatformSettingsService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PlatformPasswordRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $settings = app(PlatformSettingsService::class);

        $minLength = (int) $settings->get('min_password_length', 8);
        $requireUppercase = (bool) $settings->get('require_uppercase', false);
        $requireNumbers = (bool) $settings->get('require_numbers', false);
        $requireSpecial = (bool) $settings->get('require_special_chars', false);

        if (mb_strlen($value) < $minLength) {
            $fail(__('Le mot de passe doit contenir au moins :min caractères.', ['min' => $minLength]));
            return;
        }

        if ($requireUppercase && ! preg_match('/[A-Z]/', $value)) {
            $fail(__('Le mot de passe doit contenir au moins une lettre majuscule.'));
            return;
        }

        if ($requireNumbers && ! preg_match('/[0-9]/', $value)) {
            $fail(__('Le mot de passe doit contenir au moins un chiffre.'));
            return;
        }

        if ($requireSpecial && ! preg_match('/[^a-zA-Z0-9]/', $value)) {
            $fail(__('Le mot de passe doit contenir au moins un caractère spécial.'));
        }
    }
}
