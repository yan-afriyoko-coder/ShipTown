<?php

namespace App\Jobs;

use App\Events\HourlyEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class RunHourlyListener
 * @package App\Jobs
 */
class RunHourlyJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array|string[]
     */
    private array $jobClassesToRun = [
        \App\Jobs\Orders\ClearPackerIdJob::class,
        \App\Modules\StatusAutoPilot\src\Jobs\RefillStatusesJob::class,
    ];

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        collect($this->jobClassesToRun)->each(function ($jobClass) {
            dispatch(new $jobClass);
        });

        HourlyEvent::dispatch();

        Log::info('Hourly jobs dispatched');
    }
}
