<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Modules\MagentoApi\src\Api\StockItems;
use App\Modules\MagentoApi\src\Models\MagentoProduct;
use Grayloon\Magento\Magento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * Class SyncCheckFailedProductsJob.
 */
class FetchStockItemsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $collection = MagentoProduct::query()
            ->whereNull('stock_items_fetched_at')
            ->limit(100)
            ->get();

        $collection->each(function (MagentoProduct $product) {
            $this->fetchStockItem($product);
        });

        if ($collection->isNotEmpty()) {
            self::dispatch();
        }
    }

    private function fetchStockItem(MagentoProduct $product)
    {
        $stockItems = new StockItems(new Magento());

        $response = $stockItems->fetch($product->product->sku);

        $product->update([
            'stock_items_fetched_at' => now(),
            'stock_items_raw_import' => $response->json(),
        ]);
    }
}
