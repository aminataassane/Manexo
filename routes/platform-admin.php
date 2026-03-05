<?php

use Illuminate\Support\Facades\Route;

// All platform roles (super_admin, platform_admin, platform_observer)
Route::middleware(['auth', 'super-admin'])
    ->prefix('platform-admin')
    ->name('platform-admin.')
    ->group(function () {
        Route::get('/', App\Livewire\SuperAdmin\Dashboard::class)->name('dashboard');
        Route::get('/organizations', App\Livewire\SuperAdmin\Organizations::class)->name('organizations');
        Route::get('/organizations/{organization}', App\Livewire\SuperAdmin\OrganizationDetail::class)->name('organizations.show');
        Route::get('/audit-log', App\Livewire\SuperAdmin\AuditLog::class)->name('audit-log');
        Route::get('/audit-log/export', App\Http\Controllers\SuperAdmin\AuditExportController::class)->name('audit-log.export');
        Route::get('/users', App\Livewire\SuperAdmin\UsersGlobal::class)->name('users');
        Route::get('/files', App\Livewire\SuperAdmin\FilesStorage::class)->name('files');
        Route::get('/notifications', App\Livewire\SuperAdmin\NotificationsGlobal::class)->name('notifications');
        Route::get('/support-sessions', App\Livewire\SuperAdmin\SupportSessions::class)->name('support-sessions');
    });

// Super admin only (security, monitoring, backups)
Route::middleware(['auth', 'super-admin:super'])
    ->prefix('platform-admin')
    ->name('platform-admin.')
    ->group(function () {
        Route::get('/security', App\Livewire\SuperAdmin\SecuritySettings::class)->name('security');
        Route::get('/monitoring', App\Livewire\SuperAdmin\Monitoring::class)->name('monitoring');
        Route::get('/backups', App\Livewire\SuperAdmin\Backups::class)->name('backups');
    });

// Legacy redirect: /super-admin/* → /platform-admin/*
Route::middleware(['auth', 'super-admin'])
    ->prefix('super-admin')
    ->get('/{any?}', fn (string $any = '') => redirect('/platform-admin/' . $any))
    ->where('any', '.*');
