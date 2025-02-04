<?php

namespace App\Schedulers;

use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\AggregateProductAccesses;

class ProductAccessScheduler
{
    public function __invoke(Schedule $schedule): void
    {
        $schedule->job(new AggregateProductAccesses())->hourly();
    }
}
