<?php

use App\Livewire\Test;
use App\Livewire\Tickets\Create as CreateTicket;
use App\Livewire\Tickets\Index as TicketsIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::view('/organizations', 'organizations.select')
    ->middleware(['auth', 'verified'])
    ->name('organizations.select');

Route::view('/dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'ensure.organization'])
    ->name('dashboard');

Route::get('/tickets', TicketsIndex::class)
    ->middleware(['auth', 'verified', 'ensure.organization'])
    ->name('tickets.index');

Route::get('/tickets/create', CreateTicket::class)
    ->middleware(['auth', 'verified', 'ensure.organization'])
    ->name('tickets.create');

Route::view('/profile', 'profile')
    ->middleware(['auth', 'verified'])
    ->name('profile');

Route::get('/test', Test::class);

require __DIR__.'/auth.php';
