<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Helpers\TemporaryTable;
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
                        ->chunkById(100, function (Collection $chunk) use ($magentoConnection) {
                            $productSkus = $chunk->map(function (MagentoProduct $product) {
                                return $product->product->sku;
                            });

                            $response = MagentoApi::fetchBasePricesBulk($magentoConnection->api_access_token, $magentoConnection->base_url, $productSkus->toArray());

                            $responseRecords = collect($response->json())
                                ->filter(function ($apiBasePriceRecord) use ($magentoConnection) {
                                    return $apiBasePriceRecord['store_id'] == $magentoConnection->magento_store_id;
                                })
                                ->map(function ($apiBasePriceRecord) use ($magentoConnection) {
                                    return [
                                        'connection_id' => $magentoConnection->getKey(),
                                        'sku' => $apiBasePriceRecord['sku'],
                                        'price' => $apiBasePriceRecord['price'],
                                        'base_prices_fetched_at' => now(),
                                        'base_prices_raw_import' => json_encode($apiBasePriceRecord),
                                    ];
                                });

                            TemporaryTable::fromArray('tempTable_MagentoBasePriceFetch', $responseRecords->toArray(), function (Blueprint $table) {
                                $table->temporary();
                                $table->unsignedBigInteger('connection_id')->index();
                                $table->string('sku')->index();
                                $table->decimal('price', 20, 3)->nullable();
                                $table->dateTime('base_prices_fetched_at')->nullable();
                                $table->json('base_prices_raw_import')->nullable();
                            });

                            \DB::statement('
                                UPDATE modules_magento2api_products
                                INNER JOIN tempTable_MagentoBasePriceFetch ON modules_magento2api_products.sku = tempTable_MagentoBasePriceFetch.sku
                                SET modules_magento2api_products.exists_in_magento = 1,
                                    modules_magento2api_products.magento_price = tempTable_MagentoBasePriceFetch.price,
                                    modules_magento2api_products.base_prices_fetched_at = tempTable_MagentoBasePriceFetch.base_prices_fetched_at,
                                    modules_magento2api_products.base_prices_raw_import = tempTable_MagentoBasePriceFetch.base_prices_raw_import
                            ');

                            // Update missing records
                            MagentoProduct::query()
                                ->whereIn('id', $chunk->pluck('id'))
                                ->whereNotIn('sku', $responseRecords->pluck('sku'))
                                ->update([
                                    'exists_in_magento' => false,
                                    'base_prices_fetched_at' => now(),
                                    'base_prices_raw_import' => null
                                ]);

                            usleep(100000); // 100ms
                        });
                });
        } catch (Exception $exception) {
            report($exception);
            return;
        }
    }
}
