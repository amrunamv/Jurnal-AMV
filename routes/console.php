<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Backups
Schedule::command('backup:full')->dailyAt('02:00');
Schedule::command('backup:monitor')->dailyAt('03:00');

// Monthly Report
Schedule::command('report:monthly')->monthlyOn(1, '08:00');

// Sitemap Generation
Schedule::command('sitemap:generate')->monthlyOn(1, '01:00');
