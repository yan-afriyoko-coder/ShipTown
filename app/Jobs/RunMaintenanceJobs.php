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
        \App\Jobs\Orders\RecalculateProductLineCountJob::class,
        \App\Jobs\Orders\RecalculateTotalQuantityOrderedJob::class,
        \App\Jobs\Orders\RecalculatePickedAtForPickingOrdersJob::class,
        \App\Jobs\Orders\ClearPackerIdAssignmentJob::class,
        \App\Jobs\Inventory\RecalculateLocation999QuantityReservedJob::class,
        \App\Jobs\Products\RecalculateQuantityJob::class,
        \App\Jobs\Products\RecalculateQuantityReservedJob::class,
        \App\Jobs\RefillStatusesJob::class,
        \App\Jobs\SyncProductToApi2cart::class,
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
