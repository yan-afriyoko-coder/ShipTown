<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\Magento2MSI\src\Api\MagentoApi;
use App\Modules\MagentoApi\src\Models\MagentoConnection;
use App\Modules\MagentoApi\src\Models\MagentoProduct;
use Exception;
use Illuminate\Support\Collection;

/**
 * Class SyncCheckFailedProductsJob.
 */
class FetchBasePricesJob extends UniqueJob
{
    public function handle(): void
    {
        try {
            MagentoConnection::query()
                ->get()
                ->each(function (MagentoConnection $magentoConnection) {
                    MagentoProduct::query()
                        ->with(['product'])
                        ->where('connection_id', $magentoConnection->getKey())
                        ->whereRaw('IFNULL(exists_in_magento, 1) = 1')
                        ->whereNull('base_prices_fetched_at')
                        ->orWhereNull('base_prices_raw_import')
                        ->chunkById(50, function (Collection $chunk) use ($magentoConnection) {
                            $productSkus = $chunk->map(function (MagentoProduct $product) {
                                return $product->product->sku;
                            });

                            $response = MagentoApi::fetchBasePricesBulk($magentoConnection->api_access_token, $magentoConnection->base_url, $productSkus->toArray());

                            $responseRecords = collect($response->json())
                                ->filter(function ($apiBasePriceRecord) use ($magentoConnection) {
                                    return $apiBasePriceRecord['store_id'] == $magentoConnection->magento_store_id;
                                });

                            // Update existing records
                            $responseRecords->each(function ($apiBasePriceRecord) use ($magentoConnection) {
                                MagentoProduct::query()
                                    ->where('connection_id', $magentoConnection->getKey())
                                    ->where('sku', $apiBasePriceRecord['sku'])
                                    ->update([
                                        'exists_in_magento' => true,
                                        'magento_price' => $apiBasePriceRecord['price'],
                                        'base_prices_fetched_at' => now(),
                                        'base_prices_raw_import' => json_encode($apiBasePriceRecord),
                                    ]);
                            });

                            // Update missing records
                            MagentoProduct::query()
                                ->whereIn('id', $chunk->pluck('id'))
                                ->whereNotIn('sku', $responseRecords->pluck('sku'))
                                ->update(['exists_in_magento' => false, 'base_prices_fetched_at' => now(), 'base_prices_raw_import' => null]);

                            usleep(100000); // 100ms
                        });
                });
        } catch (Exception $exception) {
            report($exception);
            return;
        }
    }
}
