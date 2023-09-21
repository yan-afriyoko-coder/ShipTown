<?php

namespace App\Modules\QueueMonitor\src\Listeners;

use App\Modules\QueueMonitor\src\QueueMonitorServiceProvider;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Log;

class JobFailedListener
{
    public function handle(JobFailed $event)
    {
        $jobName = $event->job->resolveName();

        if (in_array($jobName, QueueMonitorServiceProvider::$ignoredJobList)) {
            return;
        }

        Log::info('Job failed', ['job' => $jobName, 'exception' => $event->exception]);
    }
}
