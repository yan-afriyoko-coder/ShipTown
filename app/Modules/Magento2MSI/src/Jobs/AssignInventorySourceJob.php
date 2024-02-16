<?php

namespace App\Modules\Magento2MSI\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\Magento2MSI\src\Api\MagentoApi;
use App\Modules\Magento2MSI\src\Models\Magento2msiConnection;
use App\Modules\Magento2MSI\src\Models\Magento2msiProduct;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AssignInventorySourceJob extends UniqueJob
{
    public function handle(): void
    {
        Magento2msiConnection::query()
            ->where('enabled', true)
            ->get()
            ->each(function (Magento2msiConnection $connection) {
                Magento2msiProduct::query()
                    ->where('connection_id', $connection->getKey())
                    ->whereNotNull('magento_product_id')
                    ->whereNull('source_assigned')
                    ->chunkById(50, function (Collection $products) use ($connection) {
                        try {
                            $response = MagentoApi::postInventoryBulkProductSourceAssign(
                                $connection,
                                $products->pluck('sku')->toArray(),
                                [
                                    'source_belfast',
                                    'source_dublin'
                                ]
                            );

                            if ($response->failed()) {
                                Log::error('Failed to fetch stock items', [
                                    'connection_id' => $connection->getKey(),
                                    'response' => $response->json(),
                                ]);
                                return false;
                            }

                            Magento2msiProduct::query()
                                ->whereIn('id', $products->pluck('id'))
                                ->update([
                                    'source_assigned' => 1,
                                    'inventory_source_items_fetched_at' => null,
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
