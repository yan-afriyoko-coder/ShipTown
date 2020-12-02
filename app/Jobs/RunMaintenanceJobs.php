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
        \App\Jobs\Maintenance\OrderProduct\RecalculateQuantityToShipJob::class,
        \App\Jobs\Maintenance\OrderProduct\RecalculateQuantityToPickJob::class,
        \App\Jobs\Maintenance\Order\RecalculateOrderProductLineCountJob::class,
        \App\Jobs\Maintenance\Order\RecalculateOrderTotalQuantityOrderedJob::class,
        \App\Jobs\Maintenance\Order\RecalculatePickedAtForPickingOrdersJob::class,
        \App\Jobs\Maintenance\Order\ClearPackerIdAssignmentJob::class,
        \App\Jobs\Maintenance\Inventory\RecalculateLocation999QuantityReservedJob::class,
        \App\Jobs\Maintenance\Product\RecalculateQuantityJob::class,
        \App\Jobs\Maintenance\Product\RecalculateQuantityReservedJob::class,
        \App\Jobs\RefillStatusesJob::class,
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
