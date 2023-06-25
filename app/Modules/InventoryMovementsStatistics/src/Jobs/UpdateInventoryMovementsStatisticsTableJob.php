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
class UpdateInventoryMovementsStatisticsTableJob implements ShouldQueue
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
        $this->ensureAllRecordsExist();
        $this->updateStatisticRecords();
    }

    private function ensureAllRecordsExist(): void
    {
        DB::statement('
            INSERT INTO inventory_movements_statistics (
                inventory_id, product_id, warehouse_id, warehouse_code, updated_at, created_at
            )
            SELECT
                inventory.id as inventory_id,
                inventory.product_id,
                inventory.warehouse_id,
                inventory.warehouse_code,
                now() as updated_at,
                now() as created_at

            FROM inventory
            LEFT JOIN inventory_movements_statistics
              ON inventory_movements_statistics.inventory_id = inventory.id

            WHERE
              inventory.last_sold_at > DATE_SUB(now(), INTERVAL 1 DAY)
              AND inventory_movements_statistics.inventory_id IS NULL
        ');
    }

    private function updateStatisticRecords(): void
    {
        DB::statement("
            UPDATE inventory_movements_statistics

            LEFT JOIN modules_inventory_movements_statistics_view
              ON inventory_movements_statistics.inventory_id = modules_inventory_movements_statistics_view.inventory_id

            SET quantity_sold_last_7_days = expected_quantity_sold_last_7_days,
                quantity_sold_last_14_days = expected_quantity_sold_last_14_days,
                quantity_sold_last_28_days = expected_quantity_sold_last_28_days,

            WHERE IFNULL(quantity_sold_last_7_days, 0) != expected_quantity_sold_last_7_days
        ");
    }
}
