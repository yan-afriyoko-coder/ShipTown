<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\MagentoApi\src\Models\MagentoProduct;
use Illuminate\Support\Collection;

/**
 * Class SyncCheckFailedProductsJob.
 */
class EnsureProductPriceIdIsFilledJob extends UniqueJob
{
    public function handle(): void
    {
        MagentoProduct::query()
            ->whereNull('product_price_id')
            ->chunkById(10, function (Collection $products) {
                MagentoProduct::query()
                    ->whereIn('id', $products->pluck('id'))
                    ->update([
                        'product_price_id' => MagentoProduct::query()
                            ->leftJoin('modules_magento2api_connections', 'modules_magento2api_products.connection_id', '=', 'modules_magento2api_connections.id')
                            ->leftJoin('products_prices', function ($join) {
                                $join->on('products_prices.product_id', '=', 'modules_magento2api_products.product_id')
                                    ->on('products_prices.warehouse_id', '=', 'modules_magento2api_connections.pricing_source_warehouse_id');
                            })
                            ->max('products_prices.id'),
                    ]);

                usleep(100000); // 0.1 second
            });
    }
}
