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
                        ->where('magento_connection_id', $magentoConnection->getKey())
                        ->whereRaw('IFNULL(exists_in_magento, 1) = 1')
                        ->whereNull('base_prices_fetched_at')
                        ->orWhereNull('base_prices_raw_import')
                        ->chunkById(100, function (Collection $chunk) use ($magentoConnection) {
                            $attributes = $chunk->map(function (MagentoProduct $product) {
                                return $product->product->sku;
                            });

                            $response = MagentoApi::fetchBasePricesBulk($magentoConnection->api_access_token, $magentoConnection->base_url, $attributes->toArray());

                            ray($response);
                            usleep(100000); // 100ms
                        });
                });
        } catch (Exception $exception) {
            report($exception);
            return;
        }
    }
}
