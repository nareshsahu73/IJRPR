<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Run queue worker every minute to process pending jobs (email sending etc.)
Schedule::command('queue:work --stop-when-empty --tries=3 --memory=128')->everyMinute()->withoutOverlapping();
