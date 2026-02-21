<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Abandoned cart recovery emails — runs every 15 minutes
Schedule::command('carts:send-reminders')->everyFifteenMinutes()->withoutOverlapping();

// Refresh exchange rates every 6 hours
Schedule::command('currency:refresh')->everySixHours()->withoutOverlapping();
