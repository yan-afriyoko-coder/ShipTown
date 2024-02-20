<?php

namespace App\Modules\Magento2MSI\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class SyncCheckFailedProductsJob.
 */
class EnsureProductRecordsExistJob extends UniqueJob
{
    public function handle()
    {
        $maxId = DB::table('products')->max('id') ?? 0;

        do {
            $minId = max($maxId - 1000, 0);

            $recordsUpdated = DB::affectingStatement("
                INSERT INTO modules_magento2msi_inventory_source_items (connection_id, product_id, inventory_totals_by_warehouse_tag_id, sku, source_code, custom_uuid, created_at, updated_at)
                SELECT
                        modules_magento2msi_connections.id as connection_id,
                        inventory_totals_by_warehouse_tag.product_id as product_id,
                        inventory_totals_by_warehouse_tag.id as inventory_totals_by_warehouse_tag_id,
                        products.sku as sku,
                        modules_magento2msi_connections.magento_source_code as source_code,
                        CONCAT(products.sku, '-', modules_magento2msi_connections.magento_source_code) as custom_uuid,
                        NOW() as created_at,
                        NOW() as updated_at

                FROM `modules_magento2msi_connections`

                INNER JOIN inventory_totals_by_warehouse_tag
                    ON modules_magento2msi_connections.inventory_source_warehouse_tag_id = inventory_totals_by_warehouse_tag.tag_id

                LEFT JOIN modules_magento2msi_inventory_source_items
                    ON modules_magento2msi_inventory_source_items.connection_id = modules_magento2msi_connections.id
                    AND modules_magento2msi_inventory_source_items.product_id = inventory_totals_by_warehouse_tag.product_id

                INNER JOIN taggables as available_online_tag
                    ON available_online_tag.tag_id IN (SELECT ID FROM `tags` WHERE JSON_EXTRACT(name, '$.en') = ?)
                    AND available_online_tag.taggable_type = ?
                    AND available_online_tag.taggable_id = inventory_totals_by_warehouse_tag.product_id

                LEFT JOIN products
                    ON products.id = inventory_totals_by_warehouse_tag.product_id

                WHERE products.id BETWEEN ? AND ?
                  AND modules_magento2msi_connections.enabled = 1
                  AND modules_magento2msi_inventory_source_items.id IS NULL
                  AND modules_magento2msi_connections.id != 3

                LIMIT 5000
           ", ['Available Online', Product::class, $minId, $maxId]);

            $maxId = $minId;

            Log::info('Job processing', [
                'job' => self::class,
                'recordsUpdated' => $recordsUpdated,
                'maxId' => $maxId,
            ]);

            usleep(100000); // 0.1 second
        } while ($maxId > 0);
    }
}
