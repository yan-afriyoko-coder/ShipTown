<?php

namespace App\Modules\Magento2MSI\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\Magento2MSI\src\Api\MagentoApi;
use App\Modules\Magento2MSI\src\Models\Magento2msiConnection;
use App\Modules\Magento2MSI\src\Models\Magento2msiProduct;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FetchStockItemsJob extends UniqueJob
{
    public function handle(): void
    {
        DB::affectingStatement("
            REPLACE INTO modules_magento2msi_products (connection_id, product_id)
            SELECT
                modules_magento2api_connections.id as connection_id,
                products.id as product_id

            FROM products

            INNER JOIN modules_magento2api_connections
                    ON 1=1

            LEFT JOIN modules_magento2msi_products
                ON modules_magento2msi_products.product_id = products.id
                AND modules_magento2msi_products.connection_id = modules_magento2api_connections.id

            WHERE modules_magento2msi_products.id IS NULL
       ");

        Magento2msiConnection::query()
            ->get()
            ->each(function (Magento2msiConnection $connection) {
                Magento2msiProduct::query()
                    ->where('connection_id', $connection->getKey())
                    ->whereNull('stock_items_fetched_at')
                    ->chunkById(50, function (Collection $products) use ($connection) {
                        try {
                            $response = MagentoApi::getInventorySourceItems($connection, $products->pluck('product.sku'));
                        } catch (Exception $exception) {
                            report($exception);
                        }
                    });
            });
    }
}
