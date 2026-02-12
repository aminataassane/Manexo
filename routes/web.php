<?php

use App\Livewire\Test;
use App\Livewire\Admin\FormBuilder as AdminFormBuilder;
use App\Livewire\Admin\Users as AdminUsers;
use App\Livewire\Admin\Settings as AdminSettings;
use App\Livewire\Reports\Index as ReportsIndex;
use App\Livewire\Tickets\Create as CreateTicket;
use App\Livewire\Tickets\Index as TicketsIndex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
 * Locale switch (FR/EN)
 */
Route::get('/locale/{locale}', function (Request $request, string $locale) {
    $locale = strtolower($locale);

    if (! in_array($locale, ['fr', 'en'], true)) {
        $locale = (string) config('app.locale', 'fr');
    }

    $request->session()->put('locale', $locale);

    $fallback = Auth::check()
        ? route('organizations.select')
        : route('home');

    $previous = url()->previous();

    return redirect()
        ->to($previous ?: $fallback)
        ->withCookie(cookie('locale', $locale, 60 * 24 * 365)) // 1 year
        ->with('profile_status', $locale === 'en' ? 'Language switched to English.' : 'Langue changée en français.');
})->where('locale', 'fr|en')->name('locale.switch');

/**
 * Application routes (must be logged in + email verified)
 */
Route::middleware(['auth', 'verified'])->group(function () {
    // Organization selection (no ensure.organization yet)
    Route::view('/organizations', 'organizations.select')->name('organizations.select');

    // Profile
    Route::view('/profile', 'profile')->name('profile');
    Route::get('/profile/history', \App\Livewire\Profile\History::class)->name('profile.history');
    Route::post('/profile/sessions/logout-all', function (Request $request) {
        $userId = Auth::id();
        if (! $userId) {
            abort(403);
        }

        $currentSessionId = $request->session()->getId();

        DB::table('sessions')
            ->where('user_id', $userId)
            ->where('id', '!=', $currentSessionId)
            ->delete();

        return back()->with('profile_status', 'Toutes les autres sessions ont été déconnectées.');
    })->name('profile.sessions.logout_all');

    // Everything below requires an organization selected
    Route::middleware(['ensure.organization'])->group(function () {
        Route::view('/dashboard', 'dashboard')->name('dashboard');

        Route::get('/tickets', TicketsIndex::class)->name('tickets.index');
        Route::get('/tickets/create', CreateTicket::class)->name('tickets.create');

        Route::get('/admin/users', AdminUsers::class)->name('admin.users');
        Route::get('/admin/settings', AdminSettings::class)->name('admin.settings');
        Route::get('/admin/forms', AdminFormBuilder::class)->name('admin.forms');
        Route::get('/reports', ReportsIndex::class)->name('reports.index');
    });
});

/**
 * Dev / sandbox routes
 */
Route::get('/test', Test::class);

/**
 * Auth routes (login/register/logout/forgot password/verify email)
 */
require __DIR__ . '/auth.php';
