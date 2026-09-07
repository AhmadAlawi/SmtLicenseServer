<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// SaaS conversion plan Phase 7 — Railway builds are async; this catches
// each instance up to 'ready'/'failed' without blocking the queue worker.
Schedule::command('railway:poll-deployments')->everyMinute()->withoutOverlapping();
