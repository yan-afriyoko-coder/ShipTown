<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\Magento2MSI\src\Api\MagentoApi;
use App\Modules\MagentoApi\src\Models\MagentoConnection;
use App\Modules\MagentoApi\src\Models\MagentoProduct;
use Illuminate\Support\Collection;

/**
 * Class SyncCheckFailedProductsJob.
 */
class SyncProductBasePricesBulkJob extends UniqueJob
{
    public function handle(): void
    {
        MagentoConnection::query()
            ->each(function (MagentoConnection $magentoConnection) {
                MagentoProduct::query()
                    ->with(['magentoConnection', 'product', 'prices'])
                    ->where(['base_price_sync_required' => true])
                    ->chunkById(10, function (Collection $chunk) use ($magentoConnection) {
                        $attributes = $chunk->map(function (MagentoProduct $magentoProduct) {
                            return [
                                'sku' => $magentoProduct->product->sku,
                                'price' => $magentoProduct->prices->price,
                                'store_id' => $magentoProduct->magentoConnection->magento_store_id ?? 0,
                            ];
                        });

                        MagentoApi::postProductsBaseBulkPrices(
                            $magentoConnection->api_access_token,
                            $magentoConnection->base_url,
                            $attributes->toArray()
                        );

                        MagentoProduct::query()
                            ->whereIn('id', $chunk->pluck('id'))
                            ->update([
                                'base_price_sync_required' => null,
                                'base_prices_fetched_at' => null,
                                'base_prices_raw_import' => null,
                                'magento_price' => null,
                            ]);
                    });
            });
    }
}
