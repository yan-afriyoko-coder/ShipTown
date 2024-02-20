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
                    ->whereNull('exists_in_magento')
                    ->chunkById(50, function (Collection $products) use ($connection) {
                        try {
                            sleep(1); // Sleep for 1 second to avoid rate limiting

                            return $this->getProductIds($connection, $products);
                        } catch (Exception $exception) {
                            report($exception);
                            throw $exception;
                        }
                    });
            });
    }

    protected function getProductIds(Magento2msiConnection $connection, Collection $products): bool
    {
        $response = MagentoApi::getProducts(
            $connection,
            $products->pluck('product.sku')
        );

        if ($response->failed()) {
            Log::error('Magento2msi - Failed to fetch product IDs', [
                'connection_id' => $connection->getKey(),
                'response' => $response->json(),
            ]);
            return false;
        }

        $map = collect($response->json('items'))
            ->map(function ($item) use ($connection) {
                return [
                    'connection_id' => $connection->getKey(),
                    'sku' => $item['sku'],
                    'exists_in_magento' => true,
                    'magento_product_id' => $item['id'],
                    'sync_required' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

        Magento2msiProduct::query()->upsert($map->toArray(), ['connection_id', 'sku'], [
            'magento_product_id',
            'exists_in_magento',
            'sync_required',
            'updated_at'
        ]);

        Magento2msiProduct::query()
            ->whereIn('id', $products->pluck('id'))
            ->whereNull('exists_in_magento')
            ->update([
                'exists_in_magento' => false,
                'magento_product_id' => null,
                'sync_required' => null,
            ]);

        Log::info('Magento2msi - Fetched ProductIDs', [
            'connection' => $connection->getKey(),
            'count' => $map->count(),
        ]);

        return true;
    }
}
