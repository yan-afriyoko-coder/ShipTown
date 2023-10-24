<?php

namespace App\Modules\Rmsapi\src\Jobs\Maintenance;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

class NotInImportTableProductsSql extends UniqueJob
{
    public function handle()
    {
        // WARNING: Use with caution! It will override the inventory
        DB::statement("
            SELECT concat('UPDATE Item SET LastUpdated = getDate() WHERE ItemLookupCode=''', products.sku, '''')
            FROM products

            LEFT JOIN modules_rmsapi_products_imports
              ON modules_rmsapi_products_imports.product_id = products.id

            WHERE modules_rmsapi_products_imports.id IS NULL

            LIMIT 50000
        ");
    }
}
