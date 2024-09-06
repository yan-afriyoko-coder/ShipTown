<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\MagentoApi\src\Exceptions\UnauthorizedException;
use App\Modules\MagentoApi\src\Models\MagentoProduct;
use App\Modules\MagentoApi\src\Services\MagentoService;
use Exception;

/**
 * Class SyncCheckFailedProductsJob.
 */
class FetchBasePricesJob extends UniqueJob
{
    public function handle(): void
    {
        MagentoProduct::query()
            ->with(['magentoConnection', 'product'])
            ->whereRaw('IFNULL(exists_in_magento, 1) = 1')
            ->whereNull('base_prices_fetched_at')
            ->orWhereNull('base_prices_raw_import')
            ->chunkById(100, function ($products) {
                $result = collect($products)
                    ->each(function (MagentoProduct $product) {
                        try {
                            MagentoService::fetchBasePrices($product);
                        } catch (UnauthorizedException $exception) {
                            report($exception);
                            return false;
                        } catch (Exception $exception) {
                            report($exception);
                            return false;
                        }

                        return true;
                    });

                usleep(100000); // 100ms
                return $result;
            });
    }
}
