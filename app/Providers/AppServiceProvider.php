<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

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
    }
}
