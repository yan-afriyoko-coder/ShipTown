<?php

namespace App\Modules\InventoryMovementsStatistics\src\Listeners;

use App\Events\SyncRequestedEvent;
use Illuminate\Support\Facades\DB;

class EveryMinuteEventListener
{
    public function handle()
    {
        DB::statement("DELETE FROM inventory_movements_statistics WHERE description = 'sold_last_7days'");
        DB::statement("
            INSERT INTO inventory_movements_statistics (description, inventory_id, product_id, warehouse_id, warehouse_code, quantity_sold, updated_at, created_at)
            SELECT
                'sold_last_7days' as description,
                inventory_id,
                product_id,
                warehouse_id,
                warehouses.code as warehouse_code,
                sum(quantity_delta),
                now() as updated_at,
                now() as created_at
            FROM `inventory_movements`
            LEFT JOIN warehouses ON warehouses.id = inventory_movements.warehouse_id

            WHERE
              `type` = 'sale'
              AND inventory_movements.created_at > DATE_ADD(now(), INTERVAL -7 DAY)

            GROUP BY
              inventory_id,
              product_id,
              warehouse_id,
              warehouses.code

        ");
    }
}
