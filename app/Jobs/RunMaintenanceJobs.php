<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunMaintenanceJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $jobClassesToRun = [
        \App\Jobs\OrderProducts\RecalculateQuantityToShipJob::class,
        \App\Jobs\OrderProducts\RecalculateQuantityToPickJob::class,
        \App\Jobs\Orders\ClearPackerIdAssignmentJob::class,
        \App\Jobs\Inventory\RecalculateLocation999QuantityReservedJob::class,
        \App\Jobs\Products\RecalculateProductQuantityJob::class,
        \App\Jobs\Products\RecalculateProductQuantityReservedJob::class,
        \App\Modules\AutoPilot\src\Jobs\RefillStatusesJob::class,
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $jobs = collect($this->jobClassesToRun)->map(function ($jobClass) {
            return new $jobClass;
        })->all();

        $this->chain($jobs);
    }
}
