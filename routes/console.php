<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\AggregateProductAccesses;
use App\Console\Commands\PreloadTopProductsCache;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Define scheduled tasks
$schedule = app(Schedule::class);

// Schedule the job to aggregate product accesses hourly
$schedule->job(new AggregateProductAccesses)->hourly();

// Schedule the command to preload the cache daily at midnight
$schedule->command(PreloadTopProductsCache::class)->dailyAt('00:00');
