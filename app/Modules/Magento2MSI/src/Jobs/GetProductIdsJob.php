<?php

namespace App\Modules\Magento2MSI\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\Magento2MSI\src\Api\MagentoApi;
use App\Modules\Magento2MSI\src\Models\Magento2msiConnection;
use App\Modules\Magento2MSI\src\Models\Magento2msiProduct;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class GetProductIdsJob extends UniqueJob
{
    public function handle(): void
    {
        Magento2msiConnection::query()
            ->where('enabled', true)
            ->get()
            ->each(function (Magento2msiConnection $connection) {
                Magento2msiProduct::query()
                    ->with('product')
                    ->where('connection_id', $connection->getKey())
                    ->whereNull('source_assigned')
                    ->chunkById(50, function (Collection $products) use ($connection) {
                        try {
                            $response = MagentoApi::getProducts(
                                $connection,
                                $products->pluck('product.sku')
                            );

                            if ($response->failed()) {
                                Log::error('Failed to fetch stock items', [
                                    'connection_id' => $connection->getKey(),
                                    'response' => $response->json(),
                                ]);
                                return false;
                            }

                            $map = collect($response->json('items'))
                                ->map(function ($item) use ($connection) {
                                    return [
                                        'magento_product_id' => $item['id'],
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

                        return true;
                    });
            });
    }
}
