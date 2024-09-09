<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\Magento2MSI\src\Api\MagentoApi;
use App\Modules\MagentoApi\src\Models\MagentoConnection;
use App\Modules\MagentoApi\src\Models\MagentoProduct;
use App\Modules\MagentoApi\src\Services\MagentoService;
use Exception;
use Illuminate\Support\Collection;

/**
 * Class SyncCheckFailedProductsJob.
 */
class FetchSpecialPricesJob extends UniqueJob
{
    public function handle(): void
    {
        try {
            MagentoConnection::query()
                ->get()
                ->each(function (MagentoConnection $magentoConnection) {
                    MagentoProduct::query()
                        ->with(['product'])
                        ->where([
                            'connection_id' => $magentoConnection->getKey(),
                            'exists_in_magento' => true,
                        ])
                        ->whereNull('special_prices_fetched_at')
                        ->orWhereNull('special_prices_raw_import')
                        ->chunkById(50, function (Collection $chunk) use ($magentoConnection) {
                            $productSkus = $chunk->map(function (MagentoProduct $product) {
                                return $product->product->sku;
                            });

                            $response = MagentoApi::fetchSpecialPricesBulk($magentoConnection->api_access_token, $magentoConnection->base_url, $productSkus->toArray());

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
                                        'magento_sale_price' => $apiBasePriceRecord['price'],
                                        'magento_sale_price_start_date' => $apiBasePriceRecord['price_from'],
                                        'magento_sale_price_end_date' => $apiBasePriceRecord['price_to'],
                                        'special_prices_fetched_at' => now(),
                                        'special_prices_raw_import' => json_encode($apiBasePriceRecord),
                                    ]);
                            });

                            // Update missing records
                            MagentoProduct::query()
                                ->whereIn('id', $chunk->pluck('id'))
                                ->whereNotIn('sku', $responseRecords->pluck('sku'))
                                ->update([
                                    'special_prices_fetched_at' => now(),
                                    'special_prices_raw_import' => null,
                                    'magento_sale_price' => null,
                                    'magento_sale_price_start_date' => null,
                                    'magento_sale_price_end_date' => null,
                                ]);

                            usleep(100000); // 100ms
                        });
                });
        } catch (Exception $exception) {
            report($exception);
            return;
        }
    }

//    public function handle(): void
//    {
//        MagentoProduct::query()
//            ->whereRaw('IFNULL(exists_in_magento, 1) = 1')
//            ->whereNull('special_prices_fetched_at')
//            ->orWhereNull('special_prices_raw_import')
//            ->chunkById(100, function ($products) {
//                $result = collect($products)
//                    ->each(function (MagentoProduct $product) {
//                        try {
//                            MagentoService::fetchSpecialPrices($product);
//                        } catch (Exception $exception) {
//                            report($exception);
//                            return false;
//                        }
//
//                        return true;
//                    });
//
//                usleep(100000); // 100ms
//
//                return $result;
//            });
//    }
}
