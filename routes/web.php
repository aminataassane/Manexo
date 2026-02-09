<?php

use App\Livewire\Test;
use App\Livewire\Tickets\Create as CreateTicket;
use App\Livewire\Tickets\Index as TicketsIndex;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Public + App)
|--------------------------------------------------------------------------
|
| - Public pages (no auth)
| - Application pages (auth/verified/organization)
| - Auth routes are defined in routes/auth.php (included at the end)
|
*/

/**
 * Public routes
 */
Route::view('/', 'home')->name('home');

/**
 * Application routes (must be logged in + email verified)
 */
Route::middleware(['auth', 'verified'])->group(function () {
    // Organization selection (no ensure.organization yet)
    Route::view('/organizations', 'organizations.select')->name('organizations.select');

    // Profile
    Route::view('/profile', 'profile')->name('profile');

    // Everything below requires an organization selected
    Route::middleware(['ensure.organization'])->group(function () {
        Route::view('/dashboard', 'dashboard')->name('dashboard');

        Route::get('/tickets', TicketsIndex::class)->name('tickets.index');
        Route::get('/tickets/create', CreateTicket::class)->name('tickets.create');
    });
});

/**
 * Dev / sandbox routes
 */
Route::get('/test', Test::class);

/**
 * Auth routes (login/register/logout/forgot password/verify email)
 */
require __DIR__.'/auth.php';
