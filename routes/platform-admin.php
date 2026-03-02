<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'super-admin'])
    ->prefix('platform-admin')
    ->name('platform-admin.')
    ->group(function () {
        Route::get('/', App\Livewire\SuperAdmin\Dashboard::class)->name('dashboard');
        Route::get('/organizations', App\Livewire\SuperAdmin\Organizations::class)->name('organizations');
        Route::get('/organizations/{organization}', App\Livewire\SuperAdmin\OrganizationDetail::class)->name('organizations.show');
        Route::get('/audit-log', App\Livewire\SuperAdmin\AuditLog::class)->name('audit-log');
    });

// Legacy redirect: /super-admin/* → /platform-admin/*
Route::middleware(['auth', 'super-admin'])
    ->prefix('super-admin')
    ->get('/{any?}', fn (string $any = '') => redirect('/platform-admin/' . $any))
    ->where('any', '.*');
