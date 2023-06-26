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
                id as inventory_id,
                product_id,
                warehouse_id,
                warehouse_code,
                now() as updated_at,
                now() as created_at
            FROM `inventory` i
            WHERE i.id NOT IN (
                SELECT inventory_id FROM inventory_movements_statistics
            )
        ');
    }

    private function updateStatisticRecords(): void
    {
        $tableName = implode('_', ['itemMovementsStatistics', rand(1000000000000, 10000000000000)]);
        DB::statement('
            CREATE TEMPORARY TABLE '. $tableName.' AS (
            SELECT inventory_movements.inventory_id,
                sum(case when inventory_movements.created_at > date_sub(now(), interval 28 day) then -quantity_delta else 0 end) as expected_quantity_sold_last_28_days,
                sum(case when inventory_movements.created_at > date_sub(now(), interval 14 day) then -quantity_delta else 0 end) as expected_quantity_sold_last_14_days,
                sum(case when inventory_movements.created_at > date_sub(now(), interval 7 day) then -quantity_delta else 0 end) as expected_quantity_sold_last_7_days,
                max(inventory_movements_statistics.quantity_sold_last_28_days) as actual_quantity_sold_last_28_days,
                max(inventory_movements_statistics.quantity_sold_last_14_days) as actual_quantity_sold_last_14_days,
                max(inventory_movements_statistics.quantity_sold_last_7_days) as actual_quantity_sold_last_7_days
            FROM inventory_movements
            RIGHT JOIN inventory_movements_statistics
              ON inventory_movements_statistics.inventory_id = inventory_movements.inventory_id
            WHERE inventory_movements.created_at > DATE_SUB(now(), INTERVAL 28 DAY)
              AND inventory_movements.type = ?
            GROUP BY inventory_movements.inventory_id
            HAVING IFNULL(expected_quantity_sold_last_28_days, 0) != IFNULL(actual_quantity_sold_last_28_days, 0)
                or IFNULL(expected_quantity_sold_last_14_days, 0) != IFNULL(actual_quantity_sold_last_14_days, 0)
                or IFNULL(expected_quantity_sold_last_7_days, 0) != IFNULL(actual_quantity_sold_last_7_days, 0)
            )
            LIMIT 5000
        ', ['sale']);

        DB::statement('
        UPDATE inventory_movements_statistics
        INNER JOIN '.$tableName.' as tempTable_123
          ON inventory_movements_statistics.inventory_id = tempTable_123.inventory_id
        SET
          inventory_movements_statistics.quantity_sold_last_28_days = tempTable_123.expected_quantity_sold_last_28_days,
          inventory_movements_statistics.quantity_sold_last_14_days = tempTable_123.expected_quantity_sold_last_14_days,
          inventory_movements_statistics.quantity_sold_last_7_days = tempTable_123.expected_quantity_sold_last_7_days
        ');
    }
}
