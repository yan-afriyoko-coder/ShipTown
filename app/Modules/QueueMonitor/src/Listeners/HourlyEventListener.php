<?php

namespace App\Modules\QueueMonitor\src\Listeners;

use App\Modules\QueueMonitor\src\Jobs\DeleteOldRecordsJob;

class HourlyEventListener
{
    public function handle()
    {
        DeleteOldRecordsJob::dispatch();
    }
}
