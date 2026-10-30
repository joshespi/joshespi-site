<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// An SMTP login that sits unused gets deactivated by the provider, and the
// first sign is an intake email that never arrives. A weekly real login keeps
// it alive, and the log shows when it stops working.
Schedule::command('mail:test')
    ->weeklyOn(1, '09:00')
    ->appendOutputTo(storage_path('logs/mail-test.log'));
