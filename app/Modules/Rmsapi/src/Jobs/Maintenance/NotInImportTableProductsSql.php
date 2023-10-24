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
            CREATE TEMPORARY TABLE aTemp_ProductsDuplicated AS
                SELECT products.id as product_id, products.sku, concat('UPDATE Item SET LastUpdated = getDate() WHERE ItemLookupCode=''', products.sku, '''') as RMS_SQL
                FROM products

                LEFT JOIN modules_rmsapi_products_imports
                  ON modules_rmsapi_products_imports.product_id = products.id

                WHERE modules_rmsapi_products_imports.id IS NULL

                LIMIT 50000;

                SELECT * FROM products

                ##UPDATE products

                INNER JOIN aTemp_ProductsDuplicated
                  ON aTemp_ProductsDuplicated.product_id = products.id

                ##SET products.sku = concat('sku_removed_','id_', products.id)

                LIMIT 500
        ");
    }
}
