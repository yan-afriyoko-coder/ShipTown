<?php

namespace App\Modules\Rmsapi\src\Jobs\Maintenance;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

class FetchSqlForUpdatingRMSJob extends UniqueJob
{
    public function handle()
    {
        DB::statement("
            SELECT
                modules_rmsapi_products_quantity_comparison_view.*, inventory.updated_at as inventory_last_updated_at, 'RMSAPI products out of sync' as whats_this
                ##CONCAT('UPDATE Item SET Quantity=', pm_quantity, ', LastUpdated=getDate() WHERE ItemLookupCode = ''', product_sku,'''; --', inventory.warehouse_code)
            FROM `modules_rmsapi_products_quantity_comparison_view`
            LEFT JOIN inventory
              ON inventory.id = modules_rmsapi_products_quantity_comparison_view.inventory_id

            WHERE
                rms_quantity != pm_quantity
                AND DATEDIFF(now(), modules_rmsapi_products_imports_updated_at) > 1
            ORDER BY inventory.warehouse_code, inventory.updated_at ASC
            LIMIT 500
        ");

        DB::statement("
            SELECT
                inventory.warehouse_code, products.sku, products.name, 'Products never synced with RMS, run query below' as whats_this
                #DISTINCT CONCAT('UPDATE Item SET LastUpdated=getDate() WHERE ItemLookupCode=''',products.sku,'''; --', inventory.warehouse_code)

            FROM inventory

            LEFT JOIN modules_rmsapi_products_imports
                ON modules_rmsapi_products_imports.warehouse_id = inventory.warehouse_id
                AND modules_rmsapi_products_imports.product_id = inventory.product_id

            LEFT JOIN products
                ON products.id = inventory.product_id

            WHERE modules_rmsapi_products_imports.id is null
                AND inventory.quantity != 0
                AND inventory.warehouse_code not in ('LTS')

            ORDER BY inventory.warehouse_id, products.sku

            LIMIT 5000
        ");

        DB::statement("
        CREATE TEMPORARY TABLE tempTable AS
        SELECT
           (SELECT quantity FROM inventory_movements where inventory_movements.inventory_id = inventory.id AND inventory_movements.created_at < '2023-02-01' LIMIT 1) as quantity_at_end_of_period,
           inventory.*

        FROM inventory

        WHERE inventory.first_movement_at < '2023-01-01'
        AND inventory.last_movement_at > '2023-02-01'
        AND IFNULL(inventory.last_counted_at, inventory.created_at) < '2022-12-25';

        SELECT products.sku, tempTable.* FROM tempTable

        LEFT JOIN products
          ON products.id = tempTable.product_id

        WHERE quantity_at_end_of_period != 0

        LIMIT 5;
        ");
    }
}
