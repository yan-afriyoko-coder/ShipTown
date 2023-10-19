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


            UPDATE inventory

            INNER JOIN inventory_movements
                ON inventory_movements.id = inventory.last_movement_id
                AND (
                    inventory_movements.quantity_after != inventory.quantity
                    OR inventory_movements.created_at != inventory.last_movement_at
                )

            SET
                  inventory.quantity = inventory_movements.quantity_after
                , inventory.last_movement_at = inventory_movements.created_at
                , inventory.updated_at = now()
        ');
    }
}
