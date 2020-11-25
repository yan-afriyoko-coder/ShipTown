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
//        \App\Jobs\Orders\EnsureCorrectQuantityToPickJob::dispatch();

        \App\Jobs\Maintenance\RecalculateOrderProductLineCountJob::dispatch();
        \App\Jobs\Maintenance\RecalculateOrderTotalQuantityOrderedJob::dispatch();
        \App\Jobs\Maintenance\RecalculateProductQuantityJob::dispatch();
        \App\Jobs\Maintenance\RecalculateProductQuantityReservedJob::dispatch();
        \App\Jobs\Maintenance\RecalculatePickedAtForPickingOrders::dispatch();
        \App\Jobs\Maintenance\RecalculateOrdersReservedJob::dispatch();

        \App\Jobs\RefillStatusesJob::dispatch();

//        \App\Jobs\Maintenance\RunPackingWarehouseRuleOnPaidOrdersJob::dispatch();
        \App\Jobs\Maintenance\ClearOrderPackerAssignmentJob::dispatch();
//        \App\Jobs\Maintenance\UpdateClosedAtIfNullJob::dispatch();
    }
}
