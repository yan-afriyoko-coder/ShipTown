<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Modules\MagentoApi\src\Models\MagentoProduct;
use App\Modules\MagentoApi\src\Services\MagentoService;
use Exception;
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
            ->inRandomOrder()
            ->limit(10)
            ->get();

        $collection->each(function (MagentoProduct $product) {
            try {
                MagentoService::fetchInventory($product);
            } catch (Exception $exception) {
                report($exception);
            }
        });

        if ($collection->isNotEmpty()) {
            self::dispatch();
        }
    }
}
