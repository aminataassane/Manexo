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
    Volt::route('/register', 'pages.auth.register')->name('register');
    Volt::route('/login', 'pages.auth.login')->name('login');

    /**
     * Password reset (guest only)
     */
    Volt::route('/forgot-password', 'pages.auth.forgot-password')->name('password.request');
    Volt::route('/reset-password/{token}', 'pages.auth.reset-password')->name('password.reset');
});

Route::middleware('auth')->group(function () {
    /**
     * Email verification + security (auth only)
     */
    Volt::route('/verify-email', 'pages.auth.verify-email')->name('verification.notice');
    Volt::route('/confirm-password', 'pages.auth.confirm-password')->name('password.confirm');

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
