<?php

namespace App\Modules\QueueMonitor\src\Listeners;

use App\Modules\QueueMonitor\src\Jobs\SampleJob;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\DB;

class JobProcessingListener
{
    public function handle($event)
    {
        /** @var Job $job */
        $job = $event->job;

        $recordsUpdated = DB::table('modules_queue_monitor_jobs')
            ->where([
                'uuid' => null,
                'job_class' => $job->payload()['displayName'],
            ])
            ->update([
                'uuid' => $job->payload()['uuid'],
                'processing_at' => now()
            ]);

        if ($recordsUpdated > 0) {
            return;
        }

        DB::table('modules_queue_monitor_jobs')
            ->insert([
                'uuid' => $job->payload()['uuid'],
                'job_class' => $job->payload()['displayName'],
                'dispatched_at' => now(),
                'processing_at' => now()
            ]);
    }
}
