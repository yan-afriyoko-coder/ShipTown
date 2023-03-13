<?php

namespace App\Modules\QueueMonitor\src\Listeners;

use Illuminate\Support\Facades\DB;

class JobProcessingListener
{
    public function handle($event)
    {
        DB::table('modules_queue_monitor_jobs')
            ->where([
                'uuid' => null,
                'job_class' => get_class($event->job),
            ])
            ->update([
                'uuid' => $event->job->getJobId(),
                'processing_at' => now()
            ]);
    }
}
