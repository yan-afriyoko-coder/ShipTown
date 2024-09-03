<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\MagentoApi\src\Models\MagentoProduct;
use App\Modules\MagentoApi\src\Services\MagentoService;
use Exception;

/**
 * Class SyncCheckFailedProductsJob.
 */
class FetchSpecialPricesJob extends UniqueJob
{
    public function handle(): void
    {
        MagentoProduct::query()
            ->whereRaw('IFNULL(exists_in_magento, 1) = 1')
            ->whereNull('special_prices_fetched_at')
            ->orWhereNull('special_prices_raw_import')
            ->chunkById(100, function ($products) {
                collect($products)->each(function (MagentoProduct $product) {
                    try {
                        MagentoService::fetchSpecialPrices($product);
                    } catch (Exception $exception) {
                        report($exception);
                    }
                });
            });
    }
}
