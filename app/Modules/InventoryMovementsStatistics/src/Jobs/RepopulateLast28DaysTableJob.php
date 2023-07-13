<?php

namespace App\Modules\InventoryMovementsStatistics\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 *
 */
class RepopulateLast28DaysTableJob implements ShouldQueue
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
        if (Schema::hasTable('modules_inventory_movements_statistics_last28days_sale_movements')) {
            DB::unprepared('
                UPDATE inventory_movements_statistics
                SET quantity_sold_last_7_days = 0,
                    quantity_sold_last_14_days = 0,
                    quantity_sold_last_28_days = 0
                WHERE quantity_sold_last_7_days != 0
                    OR quantity_sold_last_14_days != 0
                    OR quantity_sold_last_28_days != 0;

                TRUNCATE TABLE modules_inventory_movements_statistics_last28days_sale_movements;

                INSERT INTO modules_inventory_movements_statistics_last28days_sale_movements (
                    inventory_movement_id, sold_at, inventory_id, warehouse_id, quantity_sold
                )
                SELECT
                    inventory_movements.id as inventory_movement_id,
                    inventory_movements.created_at as sold_at,
                    inventory_movements.inventory_id,
                    inventory_movements.warehouse_id,
                    inventory_movements.quantity_delta * -1 as quantity_sold
                FROM `inventory_movements`
                WHERE inventory_movements.type = "sale"
                AND inventory_movements.created_at >= DATE_SUB(NOW(), INTERVAL 28 DAY);

                UPDATE modules_inventory_movements_statistics_last28days_sale_movements
                SET included_in_7days = 0
                WHERE included_in_7days is null
                AND sold_at < DATE_SUB(NOW(), INTERVAL 7 DAY);

                UPDATE modules_inventory_movements_statistics_last28days_sale_movements
                SET included_in_14days = 0
                WHERE included_in_14days is null
                AND sold_at < DATE_SUB(NOW(), INTERVAL 14 DAY);
            ');
        };
    }
}
