<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Modules\MagentoApi\src\Api\StockItems;
use App\Modules\MagentoApi\src\Models\MagentoProductInventoryComparisonView;
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
class SyncProductInventoryJob implements ShouldQueue
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
        MagentoProductInventoryComparisonView::query()
            ->whereRaw('magento_quantity != expected_quantity')
            ->get()
            ->each(function (MagentoProductInventoryComparisonView $comparison) {
                MagentoService::updateInventory(
                    $comparison->magentoProduct->product->sku,
                    $comparison->expected_quantity
                );

                $comparison->magentoProduct->update([
                    'stock_items_fetched_at' => null,
                    'stock_items_raw_import' => null,
                    'quantity'               => null,
                ]);
            });
    }
}
