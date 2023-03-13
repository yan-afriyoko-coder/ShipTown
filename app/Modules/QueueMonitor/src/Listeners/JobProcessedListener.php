<?php

namespace App\Modules\QueueMonitor\src\Listeners;

use Illuminate\Support\Facades\DB;

class JobProcessedListener
{
    public function handle($event)
    {
        DB::table('modules_queue_monitor_jobs')
            ->where([
                'uuid' =>  $event->job->getJobId(),
                'job_class' => get_class($event->job),
            ])
            ->update([
                'processed_at' => now()
            ]);
    }
}
