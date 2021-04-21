<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class RunHourlyJobs
 * @package App\Jobs
 */
class RunHourlyJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array|string[]
     */
    private array $jobClassesToRun = [
//        \App\Jobs\OrderProducts\RecalculateQuantityToShipJob::class,
//        \App\Jobs\OrderProducts\RecalculateQuantityToPickJob::class,
//        \App\Jobs\Orders\ClearPackerIdJob::class,
        \App\Jobs\Inventory\RecalculateLocation999QuantityReservedJob::class,
//        \App\Jobs\Products\RecalculateProductQuantityJob::class,
        \App\Jobs\Products\RecalculateProductQuantityReservedJob::class,
//        \App\Modules\AutoPilot\src\Jobs\RefillStatusesJob::class,
    ];

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $jobs = collect($this->jobClassesToRun)
            ->map(function ($jobClass) {
                return new $jobClass;
            })->all();

        $this->chain($jobs);
    }
}
