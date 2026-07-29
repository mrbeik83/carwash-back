<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('carwash:generate-slots --days=45')
    ->dailyAt('00:10')
    ->withoutOverlapping();

Schedule::command('model:prune')
    ->dailyAt('02:30');
