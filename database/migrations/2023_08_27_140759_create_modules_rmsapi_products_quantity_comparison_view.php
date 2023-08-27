<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            CREATE OR REPLACE VIEW modules_rmsapi_products_quantity_comparison_view AS
                SELECT
                 modules_rmsapi_products_imports.id  as record_id,
                  modules_rmsapi_products_imports.sku as product_sku,
                  modules_rmsapi_products_imports.product_id as product_id,
                  modules_rmsapi_products_imports.warehouse_id as warehouse_id,
                  modules_rmsapi_products_imports.warehouse_code,
                  modules_rmsapi_products_imports.quantity_on_hand as rms_quantity,
                  inventory.quantity as pm_quantity,
                  modules_rmsapi_products_imports.quantity_on_hand - inventory.quantity as quantity_delta,
                  inventory.id as inventory_id,
                  (
                      SELECT max(id)
                      FROM  inventory_movements
                      WHERE inventory_movements.inventory_id = inventory.id
                        AND inventory_movements.description = "stocktake"
                        AND inventory_movements.user_id = 1
                        AND inventory_movements.created_at > date_sub(now(), interval 7 day)
                  ) as movement_id

                FROM modules_rmsapi_products_imports
                INNER JOIN inventory
                   ON inventory.product_id = modules_rmsapi_products_imports.product_id
                   AND inventory.warehouse_id = modules_rmsapi_products_imports.warehouse_id

                WHERE modules_rmsapi_products_imports.id IN (
                    SELECT MAX(ID)
                    FROM modules_rmsapi_products_imports
                    GROUP BY
                        warehouse_id,
                        product_id
            );
        ');
    }
};
