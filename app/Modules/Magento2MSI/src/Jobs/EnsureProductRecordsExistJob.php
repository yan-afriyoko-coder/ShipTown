<?php

namespace App\Modules\Magento2MSI\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;
use Spatie\Tags\Tag;

/**
 * Class SyncCheckFailedProductsJob.
 */
class EnsureProductRecordsExistJob extends UniqueJob
{
    public function handle()
    {
        $tag = Tag::findOrCreate(['name' => 'Available Online']);

//        DB::statement("
//            INSERT INTO modules_magento2msi_inventory_source_items (connection_id, product_id, created_at, updated_at)
//            SELECT
//                modules_magento2msi_connections.id,
//                taggables.taggable_id,
//                now(),
//                now()
//            FROM taggables
//            INNER JOIN modules_magento2msi_connections ON modules_magento2msi_connections.enabled = 1
//            INNER JOIN products ON products.id = taggables.taggable_id
//            WHERE taggables.tag_id = ?
//            AND taggables.taggable_type = ?
//            AND taggables.taggable_id NOT IN (
//                SELECT modules_magento2api_products.product_id FROM modules_magento2api_products
//            )
//        ", [$tag->first()->getKey(), \App\Models\Product::class]);

        do {
            $recordsUpdated = DB::affectingStatement("
                INSERT INTO modules_magento2msi_inventory_source_items (connection_id, product_id, inventory_totals_by_warehouse_tag_id, sku, custom_uuid, created_at, updated_at)
                SELECT * FROM (SELECT
                    modules_magento2msi_connections.id as connection_id,
                    inventory_totals_by_warehouse_tag.product_id as product_id,
                    inventory_totals_by_warehouse_tag.id as inventory_totals_by_warehouse_tag_id,
                    products.sku as sku,
                    CONCAT(products.sku, '-', modules_magento2msi_connections.magento_source_code) as custom_uuid,
                    NOW() as created_at,
                    NOW() as updated_at

                FROM inventory_totals_by_warehouse_tag

                INNER JOIN modules_magento2msi_connections
                  ON modules_magento2msi_connections.inventory_source_warehouse_tag_id = inventory_totals_by_warehouse_tag.tag_id
                  AND modules_magento2msi_connections.enabled = 1

                LEFT JOIN modules_magento2msi_inventory_source_items
                  ON modules_magento2msi_inventory_source_items.connection_id = modules_magento2msi_connections.id
                  AND modules_magento2msi_inventory_source_items.product_id = inventory_totals_by_warehouse_tag.product_id

                LEFT JOIN products
                    ON products.id = inventory_totals_by_warehouse_tag.product_id


                WHERE modules_magento2msi_inventory_source_items.id IS NULL

                LIMIT 300) as tbl
           ");
        } while ($recordsUpdated > 0);
    }
}
