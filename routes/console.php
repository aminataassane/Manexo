<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('forms:mark-overdue')->hourly();
Schedule::command('forms:update-statuses')->everyFifteenMinutes();
Schedule::command('email:fetch')->everyMinute();
Schedule::command('sla:check')->everyFiveMinutes();
Schedule::command('queue:work --stop-when-empty --max-time=50')->everyMinute()->withoutOverlapping();
