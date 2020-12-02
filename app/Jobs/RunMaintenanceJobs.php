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
        \App\Jobs\Maintenance\RecalculateOrderProductLineCountJob::class,
        \App\Jobs\Maintenance\OrderProduct\RecalculateQuantityToShipJob::class,
        \App\Jobs\Maintenance\OrderProduct\RecalculateQuantityToPickJob::class,
        \App\Jobs\Maintenance\RecalculateOrderTotalQuantityOrderedJob::class,
        \App\Jobs\Maintenance\RecalculateProductQuantityJob::class,
        \App\Jobs\Maintenance\RecalculateProductQuantityReservedJob::class,
        \App\Jobs\Maintenance\RecalculateOrderPickedAtForPickingOrders::class,
        \App\Jobs\Maintenance\RecalculateLocation999QuantityReservedJob::class,
        \App\Jobs\RefillStatusesJob::class,
        \App\Jobs\Maintenance\ClearOrderPackerAssignmentJob::class,
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
        foreach ($this->jobClassesToRun as $jobClass) {
            dispatch(new $jobClass);
        }
    }
}
