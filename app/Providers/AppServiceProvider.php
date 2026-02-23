<?php

namespace App\Providers;

use App\Http\Middleware\EnsureOrganizationIsSelected;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Broadcast;
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

        Broadcast::routes(['middleware' => ['web', 'auth']]);

        Paginator::defaultView('vendor.pagination.manexo');

        // Ensure the organization middleware runs on Livewire update requests,
        // so request()->attributes->get('currentOrganization') is always available.
        Livewire::addPersistentMiddleware(EnsureOrganizationIsSelected::class);
    }
}
