<?php

namespace App\Jobs;

use App\Helpers\StockItems;
use App\Models\Product;
use Grayloon\Magento\Magento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncCheckFailedWithMagentoApi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $magento = new Magento();

        $stockItems = new StockItems($magento);

        Product::withAllTags(['CHECK FAILED'])
            ->limit(10)
            ->each(function (Product $product) use ($stockItems) {
                $params = [
                    'is_in_stock' => $product->quantity_available > 0,
                    'qty' => $product->quantity_available,
                ];
                $response = $stockItems->update($product->sku, $params);

                Log::info('MagentoApi: stockItem updated', [
                    'sku' => $product->sku,
                    'params' => $params,
                    'response_status_code' => $response->status(),
                    'response_body' => $response->json(),
                ]);
            });
    }
}
