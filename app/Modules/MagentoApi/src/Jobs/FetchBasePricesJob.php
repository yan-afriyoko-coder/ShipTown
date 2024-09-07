<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Abstracts\UniqueJob;
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
            MagentoProduct::query()
                ->with(['magentoConnection', 'product'])
                ->whereRaw('IFNULL(exists_in_magento, 1) = 1')
                ->whereNull('base_prices_fetched_at')
                ->orWhereNull('base_prices_raw_import')
                ->chunkById(100, function (Collection $chunk) {
                    $chunk->each(function (MagentoProduct $product) {
                        MagentoService::fetchBasePrices($product);
                    });

                    usleep(100000); // 100ms
                });
        } catch (Exception $exception) {
            report($exception);
            return;
        }
    }
}
