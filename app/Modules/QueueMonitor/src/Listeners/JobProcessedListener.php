<?php

namespace App\Modules\QueueMonitor\src\Listeners;

use Exception;
use Illuminate\Support\Facades\DB;

class JobProcessedListener
{
    public function handle($event)
    {
        try {
            DB::table('modules_queue_monitor_jobs')
                ->where('uuid', $event->job->payload()['uuid'])
                ->update(['processed_at' => now()]);
        } catch (Exception $e) {
            // do nothing
        }
    }
}
