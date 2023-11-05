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
            DROP TABLE IF EXISTS aTemp_ProductsDuplicated;

            CREATE TABLE aTemp_ProductsDuplicated AS
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

            LIMIT 500;

INSERT INTO inventory_movements (occurred_at, type, inventory_id, product_id, warehouse_id, quantity_after, created_at, updated_at, user_id, description)
SELECT

 now() as occurred_at,
 'stocktake' as type,
 inventory.id as inventory_id,
 inventory.product_id,
 inventory.warehouse_id,
 0 as quantity_after,
 now() as created_at,
 now() as updated_at,
 1 as user_id,
 'clearing duplicated RMS products' as description

FROM `products`

INNER JOIN inventory
  ON inventory.product_id = products.id
  AND (inventory.quantity != 0 OR inventory.quantity_reserved != 0)

WHERE `sku` LIKE 'sku_removed_%'

## SELECT
## concat('UPDATE Item SET LastUpdated = getDate() WHERE id IN (SELECT ItemID FROM Alias WHERE Alias=''', products_aliases.alias, ''');') as RMS_SQL
##
## FROM `products`
##
## INNER JOIN products_aliases
##   ON products_aliases.product_id = products.id
##
## WHERE `sku` LIKE 'sku_removed_%'

##DELETE FROM products_aliases WHERE product_id IN (SELECT id
##FROM `products`
##
##WHERE `sku` LIKE 'sku_removed_%')
        ");
    }
}
