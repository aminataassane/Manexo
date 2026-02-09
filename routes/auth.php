<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| This project uses Livewire Volt pages stored under:
| resources/views/livewire/pages/auth/*.blade.php
|
*/

Route::middleware('guest')->group(function () {
    Volt::route('/register', 'pages.auth.register')->name('register');
    Volt::route('/login', 'pages.auth.login')->name('login');

    Volt::route('/forgot-password', 'pages.auth.forgot-password')->name('password.request');
    Volt::route('/reset-password/{token}', 'pages.auth.reset-password')->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Volt::route('/verify-email', 'pages.auth.verify-email')->name('verification.notice');
    Volt::route('/confirm-password', 'pages.auth.confirm-password')->name('password.confirm');

    Route::get('/verify-email/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('dashboard');
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    Route::post('/logout', function (Request $request) {
        auth()->guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    })->name('logout');
});

