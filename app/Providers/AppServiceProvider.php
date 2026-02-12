<?php

namespace App\Providers;

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
        // Locale from session (FR/EN)
        if (! app()->runningInConsole() && request()->hasSession()) {
            app()->setLocale((string) request()->session()->get('locale', config('app.locale')));
        }
    }
}
