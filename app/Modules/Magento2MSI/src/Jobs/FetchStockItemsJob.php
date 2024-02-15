<?php

namespace App\Modules\Magento2MSI\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\Magento2MSI\src\Api\MagentoApi;
use App\Modules\Magento2MSI\src\Models\Magento2msiConnection;
use App\Modules\Magento2MSI\src\Models\Magento2msiProduct;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class FetchStockItemsJob extends UniqueJob
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
                    ->whereNull('inventory_source_items_fetched_at')
                    ->chunkById(50, function (Collection $products) use ($connection) {
                        try {
                            $response = MagentoApi::getInventorySourceItems($connection, $products->pluck('product.sku'));

                            if ($response->failed()) {
                                Log::error('Failed to fetch stock items', [
                                    'connection_id' => $connection->getKey(),
                                    'response' => $response->json(),
                                ]);
                                return false;
                            }

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
