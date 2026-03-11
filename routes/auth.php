<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Volt pages live in:
| resources/views/livewire/pages/auth/*.blade.php
|
*/

Route::middleware('guest')->group(function () {
    /**
     * Auth pages (guest only)
     */
    Volt::route('/register', 'pages.auth.register')
        ->middleware('throttle:6,1')
        ->name('register');
    Volt::route('/login', 'pages.auth.login')
        ->middleware('throttle:10,1')
        ->name('login');

    /**
     * Platform admin login (separate page, rate limited)
     */
    Volt::route('/platform-admin/login', 'pages.auth.platform-login')
        ->middleware('throttle:platform-login')
        ->name('platform-admin.login');

    /**
     * Password reset (guest only, rate limited)
     */
    Volt::route('/forgot-password', 'pages.auth.forgot-password')
        ->middleware('throttle:password-reset')
        ->name('password.request');
    Volt::route('/reset-password/{token}', 'pages.auth.reset-password')
        ->middleware('throttle:password-reset')
        ->name('password.reset');
});

Route::middleware('auth')->group(function () {
    /**
     * Email verification + security (auth only)
     */
    Volt::route('/verify-email', 'pages.auth.verify-email')->name('verification.notice');
    Volt::route('/confirm-password', 'pages.auth.confirm-password')->name('password.confirm');

    /**
     * Two-factor authentication
     */
    Route::get('/two-factor/setup', \App\Livewire\Auth\TwoFactorSetup::class)->name('two-factor.setup');
    Route::get('/two-factor/challenge', \App\Livewire\Auth\TwoFactorChallenge::class)
        ->middleware('throttle:5,1')
        ->name('two-factor.challenge');

    /**
     * Logout (auth only)
     */
    Route::post('/logout', function (Request $request) {
        auth()->guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    })->name('logout');
});
