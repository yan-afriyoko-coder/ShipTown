<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Modules\MagentoApi\src\Api\StockItems;
use App\Modules\MagentoApi\src\Models\MagentoProductInventoryComparisonView;
use App\Modules\MagentoApi\src\Models\MagentoProductPricesComparisonView;
use App\Modules\MagentoApi\src\Services\MagentoService;
use Grayloon\Magento\Magento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\Response;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class SyncCheckFailedProductsJob.
 */
class SyncProductBasePricesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        MagentoProductPricesComparisonView::query()
            ->whereRaw('magento_price != expected_price')
            ->get()
            ->each(function (MagentoProductPricesComparisonView $comparison) {
                MagentoService::updateBasePrice(
                    $comparison->magentoProduct->product->sku,
                    $comparison->expected_price,
                    $comparison->magento_store_id
                );

                $comparison->magentoProduct->update([
                    'base_prices_fetched_at' => null,
                    'base_prices_raw_import' => null,
                    'magento_price'          => null,
                ]);
            });
    }
}
