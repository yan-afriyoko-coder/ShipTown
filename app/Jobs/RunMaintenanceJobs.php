<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
        \App\Jobs\Maintenance\RecalculateOrderProductLineCountJob::dispatch();
        \App\Jobs\Maintenance\RecalculateOrderTotalQuantityOrderedJob::dispatch();
        \App\Jobs\Maintenance\RecalculateProductQuantityJob::dispatch();
        \App\Jobs\Maintenance\RecalculateProductQuantityReservedJob::dispatch();
        \App\Jobs\Maintenance\RecalculateOrderProductQuantityPicked::dispatch();
        \App\Jobs\Maintenance\RecalculatePickedAtForPickingOrders::dispatch();


        \App\Jobs\Maintenance\UpdateAllProcessingIfPaidJob::dispatch();

        \App\Jobs\Maintenance\RefillPackingWarehouseJob::dispatch();
        \App\Jobs\Maintenance\SingleLineOrdersJob::dispatch();

        \App\Jobs\Maintenance\RefillPickingJob::dispatch();

        \App\Jobs\Maintenance\MakeSureOrdersAreOnPicklist::dispatch();
        \App\Jobs\Maintenance\RunPackingWarehouseRuleOnPaidOrdersJob::dispatch();
        \App\Jobs\Maintenance\ClearOrderPackerAssignmentJob::dispatch();
        \App\Jobs\Maintenance\UpdateClosedAtIfNullJob::dispatch();
    }
}
