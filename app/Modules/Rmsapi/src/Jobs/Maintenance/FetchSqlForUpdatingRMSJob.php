<?php

namespace App\Modules\Rmsapi\src\Jobs\Maintenance;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

class FetchSqlForUpdatingRMSJob extends UniqueJob
{
    public function handle()
    {
        DB::statement("
            SELECT CONCAT('UPDATE Item SET Quantity=', pm_quantity, ', LastUpdated=getDate() WHERE ItemLookupCode = ''', product_sku,'''; --', warehouse_code)
            FROM `modules_rmsapi_products_quantity_comparison_view`
            WHERE rms_quantity != pm_quantity
            ORDER BY warehouse_code
            LIMIT 500
        ");

        DB::statement("
            SELECT DISTINCT CONCAT('UPDATE Item SET LastUpdated=getDate() WHERE ItemLookupCode=''',products.sku,'''; --', inventory.warehouse_code)
            FROM inventory

            LEFT JOIN modules_rmsapi_products_imports
             ON modules_rmsapi_products_imports.warehouse_id = inventory.warehouse_id
             AND modules_rmsapi_products_imports.product_id = inventory.product_id

            LEFT JOIN products
              ON products.id = inventory.product_id

            WHERE modules_rmsapi_products_imports.id is null
            and inventory.quantity != 0

            LIMIT 5000
        ");
    }
}
