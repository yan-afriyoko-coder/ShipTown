<?php

namespace App\Modules\QueueMonitor\src\Listeners;

use Exception;
use Illuminate\Support\Facades\DB;

class JobProcessingListener
{
    public function handle($event)
    {
        try {
            if ($event->job->getConnectionName() !== 'sync') {
                DB::table('modules_queue_monitor_jobs')
                    ->where([
                        'uuid' => null,
                        'job_class' => $event->job->payload()['displayName']])
                    ->oldest('dispatched_at')
                    ->update(['uuid' => $event->job->payload()['uuid'], 'processing_at' => now()]);
            } else {
                DB::table('modules_queue_monitor_jobs')->insert([
                    'uuid' => $event->job->payload()['uuid'],
                    'job_class' => $event->job->payload()['displayName'],
                    'dispatched_at' => now(),
                    'processing_at' => now()
                ]);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
