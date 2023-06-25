<?php

namespace App\Modules\InventoryMovementsStatistics\src\Jobs;

use App\Helpers\TemporaryTable;
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
            CREATE TEMPORARY TABLE tempTable_123 AS (
                SELECT inventory_movements.inventory_id,
                    sum(case when inventory_movements.created_at > date_sub(now(), interval 28 day) then -quantity_delta else 0 end) as expected_quantity_sold_last_28_days,
                    sum(case when inventory_movements.created_at > date_sub(now(), interval 14 day) then -quantity_delta else 0 end) as expected_quantity_sold_last_14_days,
                    sum(case when inventory_movements.created_at > date_sub(now(), interval 7 day) then -quantity_delta else 0 end) as expected_quantity_sold_last_7_days,
                    max(inventory_movements_statistics.quantity_sold_last_28_days) as actual_quantity_sold_last_28_days,
                    max(inventory_movements_statistics.quantity_sold_last_14_days) as actual_quantity_sold_last_14_days,
                    max(inventory_movements_statistics.quantity_sold_last_7_days) as actual_quantity_sold_last_7_days
                FROM inventory_movements
                LEFT JOIN inventory_movements_statistics
                  ON inventory_movements_statistics.inventory_id = inventory_movements.inventory_id
                WHERE inventory_movements.created_at > DATE_SUB(now(), INTERVAL 28 DAY)
                  AND inventory_movements.type = 'sale'
                GROUP BY inventory_movements.inventory_id
                HAVING expected_quantity_sold_last_28_days != actual_quantity_sold_last_28_days
                    or expected_quantity_sold_last_14_days != actual_quantity_sold_last_14_days
                    or expected_quantity_sold_last_7_days != actual_quantity_sold_last_7_days
            );

            UPDATE inventory_movements_statistics
            RIGHT JOIN tempTable_123
              ON tempTable_123.inventory_id = inventory_movements_statistics.inventory_id
            SET inventory_movements_statistics.quantity_sold_last_28_days = tempTable_123.expected_quantity_sold_last_28_days
                , inventory_movements_statistics.quantity_sold_last_14_days = tempTable_123.expected_quantity_sold_last_14_days
                , inventory_movements_statistics.quantity_sold_last_7_days = tempTable_123.expected_quantity_sold_last_7_days
        ");
    }
}
