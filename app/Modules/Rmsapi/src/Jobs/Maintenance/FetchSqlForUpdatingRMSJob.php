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
                modules_rmsapi_products_quantity_comparison_view.*, inventory.updated_at as inventory_last_updated_at
                #CONCAT('UPDATE Item SET Quantity=', pm_quantity, ', LastUpdated=getDate() WHERE ItemLookupCode = ''', product_sku,'''; --', warehouse_code)
            FROM `modules_rmsapi_products_quantity_comparison_view`
            LEFT JOIN inventory
              ON inventory.id = modules_rmsapi_products_quantity_comparison_view.inventory_id
            LEFT JOIN modules_magento2api_products
            WHERE rms_quantity != pm_quantity
            ORDER BY inventory_last_updated_at, warehouse_code
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
    }
}
