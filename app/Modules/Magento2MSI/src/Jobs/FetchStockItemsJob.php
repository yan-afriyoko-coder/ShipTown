<?php

namespace App\Modules\Magento2MSI\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\Magento2MSI\src\Api\MagentoApi;
use App\Modules\Magento2MSI\src\Models\Magento2msiConnection;
use App\Modules\Magento2MSI\src\Models\Magento2msiProduct;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FetchStockItemsJob extends UniqueJob
{
    public function handle(): void
    {
        DB::affectingStatement("
                INSERT INTO modules_magento2msi_inventory_source_items (connection_id, product_id, inventory_totals_by_warehouse_tag_id, custom_uuid, created_at, updated_at)
                SELECT * FROM (SELECT
                    modules_magento2msi_connections.id as connection_id,
                    inventory_totals_by_warehouse_tag.product_id as product_id,
                    inventory_totals_by_warehouse_tag.id as inventory_totals_by_warehouse_tag_id,
                    CONCAT(products.sku, '-', modules_magento2msi_connections.magento_source_code) as custom_uuid,
                    NOW() as created_at,
                    NOW() as updated_at

                FROM inventory_totals_by_warehouse_tag

                INNER JOIN modules_magento2msi_connections
                  ON modules_magento2msi_connections.inventory_source_warehouse_tag_id = inventory_totals_by_warehouse_tag.tag_id

                LEFT JOIN modules_magento2msi_inventory_source_items
                  ON modules_magento2msi_inventory_source_items.connection_id = modules_magento2msi_connections.id
                  AND modules_magento2msi_inventory_source_items.product_id = inventory_totals_by_warehouse_tag.product_id

                LEFT JOIN products
                    ON products.id = inventory_totals_by_warehouse_tag.product_id



                WHERE modules_magento2msi_inventory_source_items.id IS NULL

                LIMIT 300) as tbl
       ");

        Magento2msiConnection::query()
            ->get()
            ->each(function (Magento2msiConnection $connection) {
                MagentoApi::getModules($connection);
                MagentoApi::getInventorySources($connection);

                Magento2msiProduct::query()
                    ->with('product')
                    ->where('connection_id', $connection->getKey())
                    ->whereNull('inventory_source_items_fetched_at')
                    ->chunkById(50, function (Collection $products) use ($connection) {
                        try {
                            $response = MagentoApi::getInventorySourceItems($connection, $products->pluck('product.sku'));

                            Magento2msiProduct::query()
                                ->whereIn('id', $products->pluck('id'))
                                ->update(['inventory_source_items_fetched_at' => now(), 'inventory_source_items' => null, 'sync_required' => null]);

                            $map = collect($response->json('items'))
                                ->map(function ($item) use ($connection) {
                                    return [
                                        'connection_id' => $connection->getKey(),
                                        'sync_required' => null,
                                        'custom_uuid' => $item['sku'] . '-' . $item['source_code'],
                                        'sku' => $item['sku'],
                                        'source_code' => $item['source_code'],
                                        'quantity' => $item['quantity'],
                                        'status' => $item['status'],
                                        'inventory_source_items_fetched_at' => now(),
                                        'inventory_source_items' => json_encode($item),
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ];
                                });

                            Magento2msiProduct::query()->upsert($map->toArray(), ['custom_uuid'], [
                                'sync_required',
                                'sku',
                                'source_code',
                                'quantity',
                                'status',
                                'inventory_source_items_fetched_at',
                                'inventory_source_items',
                                'updated_at'
                            ]);

                            Log::info('Fetched stock items', [
                                'connection' => $connection->getKey(),
                                'response' => $response->json('items'),
                            ]);
                        } catch (Exception $exception) {
                            report($exception);
                            throw $exception;
                        }
                    });
            });
    }
}
