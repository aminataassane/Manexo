<?php

use App\Livewire\Test;
use App\Livewire\Admin\FormBuilder as AdminFormBuilder;
use App\Livewire\Admin\Users as AdminUsers;
use App\Livewire\Admin\Settings as AdminSettings;
use App\Livewire\Admin\FormResponses as AdminFormResponses;
use App\Livewire\Reports\Index as ReportsIndex;
use App\Livewire\UserForms\Index as UserFormsIndex;
use App\Livewire\UserForms\Fill as UserFormsFill;
use App\Livewire\UserForms\FillTeam as UserFormsFillTeam;
use App\Livewire\UserForms\FillTeamBySlug as UserFormsFillTeamBySlug;
use App\Livewire\Tickets\Create as CreateTicket;
use App\Livewire\Tickets\Index as TicketsIndex;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\PlatformInvitationController;
use App\Http\Controllers\PublicFormController;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

// Public Form Builder (published forms)
Route::middleware(['throttle:20,1'])->group(function () {
    // Redirect GET /f/slug/ → /f/slug so link with trailing slash works
    Route::get('/f/{slug}/', fn (string $slug) => redirect()->to('/f/' . trim($slug, '/'), 301))->where('slug', '.+');
    Route::get('/f/{slug}', [PublicFormController::class, 'show'])->name('forms.public.show')->where('slug', '[^/]+');
    Route::post('/f/{slug}', [PublicFormController::class, 'submit'])->name('forms.public.submit')->where('slug', '[^/]+');
});

/**
 * Locale switch (FR/EN)
 */
Route::get('/locale/{locale}', function (Request $request, string $locale) {
    $locale = strtolower($locale);

    if (! in_array($locale, ['fr', 'en'], true)) {
        $locale = (string) config('app.locale', 'fr');
    }

    $request->session()->put('locale', $locale);
    $request->session()->save();

    $fallback = Auth::check()
        ? route('dashboard')
        : route('home');

    $previous = $request->headers->get('referer');
    $target = $previous && ! str_contains($previous, '/locale/') ? $previous : $fallback;

    return redirect()
        ->to($target)
        ->withCookie(cookie('locale', $locale, 60 * 24 * 365, '/', null, null, true))
        ->with('profile_status', $locale === 'en' ? __('Language switched to English.') : __('Langue changée en français.'));
})->where('locale', 'fr|en')->name('locale.switch');

/**
 * Invitation acceptance (public, no auth required)
 */
Route::get('/invitations/{token}/accept', [InvitationController::class, 'accept'])
    ->name('invitations.accept');
Route::post('/invitations/{token}/accept', [InvitationController::class, 'processAccept'])
    ->name('invitations.process-accept');

/**
 * Platform invitation acceptance (public, no auth required)
 */
Route::get('/platform-invitations/{token}/accept', [PlatformInvitationController::class, 'accept'])
    ->name('platform-invitations.accept');
Route::post('/platform-invitations/{token}/accept', [PlatformInvitationController::class, 'processAccept'])
    ->name('platform-invitations.process-accept');

/**
 * Application routes (must be logged in + email verified)
 */
Route::middleware(['auth', 'verified'])->group(function () {
    // Organization selection (no ensure.organization yet)
    Route::view('/organizations', 'organizations.select')->name('organizations.select');

    // Everything below requires an organization selected
    Route::middleware(['ensure.organization'])->group(function () {
        // Profile (needs current organization for role + branding)
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

        Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');

        Route::get('/forms', UserFormsIndex::class)->name('forms.index');
        Route::get('/forms/team/{form}', UserFormsFillTeam::class)->name('forms.fill-team');
        Route::get('/forms/l/{slug}', UserFormsFillTeamBySlug::class)->name('forms.fill-team-by-slug');
        Route::get('/forms/{assignment}', UserFormsFill::class)->name('forms.fill');

        Route::get('/notifications', \App\Livewire\Notifications\Index::class)->name('notifications.index');

        Route::get('/discussions/{ticket?}', \App\Livewire\Discussions\Index::class)->name('discussions.index');

        Route::get('/tickets', TicketsIndex::class)->name('tickets.index');
        Route::get('/tickets/groups', \App\Livewire\Tickets\Groups::class)->name('tickets.groups');
        Route::get('/tickets/create', CreateTicket::class)->name('tickets.create');
        Route::get('/tickets/{ticket}/files/{filename}', function (Ticket $ticket, string $filename) {
            $user = Auth::user();
            if (! $user || ! $ticket->hasDiscussionAccess((int) $user->id)) {
                abort(403);
            }
            /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
            $storage = Storage::disk('local');
            $path = 'ticket-messages/' . $ticket->id . '/' . basename($filename);
            if (! $storage->exists($path)) {
                abort(404);
            }
            return $storage->response($path, $filename, [
                'Content-Type' => $storage->mimeType($path),
            ]);
        })->where('filename', '[^/]+')->name('tickets.discussion.file');

        Route::get('/tickets/{ticket}/attachment/{filename}', function (Ticket $ticket, string $filename) {
            $user = Auth::user();
            if (! $user || ! $ticket->hasDiscussionAccess((int) $user->id)) {
                abort(403);
            }
            /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
            $storage = Storage::disk('local');
            $path = 'ticket-attachments/org-' . $ticket->organization_id . '/ticket-' . $ticket->id . '/' . basename($filename);
            if (! $storage->exists($path)) {
                abort(404);
            }
            return $storage->response($path, $filename, [
                'Content-Type' => $storage->mimeType($path),
            ]);
        })->where('filename', '[^/]+')->name('tickets.attachment');
        Route::get('/tickets/{ticket}', \App\Livewire\Tickets\Discussion::class)->name('tickets.discussion');

        Route::get('/admin/users', AdminUsers::class)->name('admin.users');
        Route::get('/admin/settings', AdminSettings::class)->name('admin.settings');
        Route::get('/admin/forms', AdminFormBuilder::class)->name('admin.forms');
        Route::get('/admin/forms/{form}/responses', AdminFormResponses::class)->name('admin.forms.responses');
        Route::get('/admin/forms/responses/{response}/file/{fieldKey}', [PublicFormController::class, 'serveFile'])->where('fieldKey', '[a-zA-Z0-9_]+')->name('admin.forms.responses.file');
        Route::get('/reports', ReportsIndex::class)->name('reports.index');
        Route::get('/reports/tasks', \App\Livewire\Reports\TaskReport::class)->name('reports.tasks');
        Route::get('/reports/daily', \App\Livewire\Reports\DailyReport::class)->name('reports.daily');
        Route::get('/reports/tasks/export/{format}', [\App\Http\Controllers\TaskReportExportController::class, '__invoke'])
            ->where('format', 'csv|pdf')
            ->name('reports.tasks.export');
    });
});

/**
 * Dev / sandbox routes
 */
Route::get('/test', Test::class);

/**
 * Shared reports (signed URL, no auth required)
 */
Route::get('/reports/tasks/shared', \App\Http\Controllers\SharedTaskReportController::class)
    ->name('reports.tasks.shared')
    ->middleware('signed');

/**
 * Auth routes (login/register/logout/forgot password/verify email)
 */
require __DIR__ . '/auth.php';
