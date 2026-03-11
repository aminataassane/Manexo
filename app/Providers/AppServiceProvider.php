<?php

namespace App\Providers;

use App\Http\Middleware\EnsureOrganizationIsSelected;
use App\Models\DiscussionThread;
use App\Models\Form;
use App\Models\Organization;
use App\Models\Ticket;
use App\Policies\DiscussionThreadPolicy;
use App\Policies\FormPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\TicketPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Broadcast;
use App\Enums\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Locale is handled by web middleware to ensure
        // sessions/cookies are available (and decrypted).

        // Password complexity rules
        Password::defaults(fn () => Password::min(8)->letters()->mixedCase()->numbers());

        Broadcast::routes(['middleware' => ['web', 'auth']]);

        Paginator::defaultView('vendor.pagination.manexo');

        // Ensure the organization middleware runs on Livewire update requests,
        // so request()->attributes->get('currentOrganization') is always available.
        Livewire::addPersistentMiddleware(EnsureOrganizationIsSelected::class);

        // Register authorization policies
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::policy(Form::class, FormPolicy::class);
        Gate::policy(DiscussionThread::class, DiscussionThreadPolicy::class);
        Gate::policy(Organization::class, OrganizationPolicy::class);

        // Admin access gate: user must have at least one admin-level permission
        Gate::define('accessAdmin', function ($user) {
            return $user->hasAnyPermission([
                Permission::TeamInvite,
                Permission::TeamEditRole,
                Permission::TeamRemove,
                Permission::SettingsManageBranding,
                Permission::SettingsManageCategories,
                Permission::SettingsManagePriorities,
                Permission::SettingsManageFunctions,
                Permission::SettingsManageForms,
                Permission::SettingsManageRoles,
                Permission::SettingsDeleteOrg,
                Permission::FormsManage,
                Permission::FormsAssign,
                Permission::FormsViewResponses,
            ]);
        });

        // Rate limiters
        RateLimiter::for('password-reset', fn (Request $request) =>
            Limit::perMinute(3)->by($request->ip())
        );

        RateLimiter::for('ticket-create', fn (Request $request) =>
            Limit::perMinute(10)->by($request->user()?->id ?: $request->ip())
        );

        RateLimiter::for('message-send', fn (Request $request) =>
            Limit::perMinute(30)->by($request->user()?->id ?: $request->ip())
        );

        RateLimiter::for('file-upload', fn (Request $request) =>
            Limit::perMinute(20)->by($request->user()?->id ?: $request->ip())
        );

        RateLimiter::for('platform-login', fn (Request $request) =>
            Limit::perMinute(5)->by($request->ip())
        );

        RateLimiter::for('admin-actions', fn (Request $request) =>
            Limit::perMinute(30)->by($request->user()?->id ?: $request->ip())
        );
    }
}
