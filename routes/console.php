<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// SaaS conversion plan Phase 7 — Railway builds are async; this catches
// each instance up to 'ready'/'failed' without blocking the queue worker.
// withoutOverlapping() defaults to a 24-HOUR mutex expiry — CACHE_STORE is
// 'database', so that lock survives container restarts. A deploy that
// kills schedule:work mid-run (no chance to release it) then blocks every
// future run for up to a day. Bound it to a few minutes instead — long
// enough to cover a real run, short enough that a stuck lock self-heals
// fast. (Found live: a stuck lock from an earlier redeploy silently
// blocked this from firing at all until manually cleared.)
Schedule::command('railway:poll-deployments')->everyMinute()->withoutOverlapping(5);
