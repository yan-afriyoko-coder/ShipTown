<?php

namespace App\Modules\InventoryMovementsStatistics\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class EnsureInventoryMovementsStatisticsRecordsExistJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::statement("
            UPDATE inventory_movements_statistics

            LEFT JOIN modules_inventory_movements_statistics_view
              ON inventory_movements_statistics.inventory_id = modules_inventory_movements_statistics_view.inventory_id

            SET quantity_sold_last_7_days = expected_quantity_sold_last_7_days

            WHERE IFNULL(quantity_sold_last_7_days, 0) != expected_quantity_sold_last_7_days
        ");
    }
}
