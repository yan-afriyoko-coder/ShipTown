<?php

namespace App\Modules\Rmsapi\src\Jobs\Maintenance;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

class ResyncStockJob extends UniqueJob
{
    public function handle()
    {
        // WARNING: Use with caution! It will override the inventory
        DB::statement('
            INSERT INTO inventory_movements (
                occurred_at,
                type,
                inventory_id,
                product_id,
                warehouse_id,
                quantity_after,
                description,
                user_id,
                created_at,
                updated_at
            )
            SELECT
                now(),
                "stocktake" as type,
                inventory_id,
                product_id,
                warehouse_id,
                rms_quantity as quantity_after,
                "RMS sync" as description,
                1 as user_id,
                now() as created_at,
                now() updated_at

            FROM modules_rmsapi_products_quantity_comparison_view
            WHERE quantity_delta != 0
            AND DATEDIFF(now(), modules_rmsapi_products_imports_updated_at) > 1;


            WITH tbl AS (
                SELECT
                    inventory.id as inventory_id,
                    max(inventory_movements.id) as last_movement_id

                FROM inventory
                INNER JOIN inventory_movements
                  ON inventory_movements.inventory_id = inventory.id
                  AND inventory_movements.id > IFNULL(inventory.last_movement_id, 0)

                GROUP BY inventory.id

                LIMIT 50000
            )

            UPDATE inventory
            INNER JOIN tbl
             ON tbl.inventory_id = inventory.id
            INNER JOIN inventory_movements
             ON inventory_movements.id = tbl.last_movement_id
            SET
                inventory.quantity = inventory_movements.quantity_after,
                inventory.last_movement_id = tbl.last_movement_id,
                inventory.last_movement_at = inventory_movements.created_at,
                inventory.updated_at = now();
        ');
    }
}
