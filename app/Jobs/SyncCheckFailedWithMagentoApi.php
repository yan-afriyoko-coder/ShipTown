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

/**
 * Class SyncCheckFailedWithMagentoApi
 * @package App\Jobs
 */
class SyncCheckFailedWithMagentoApi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Product|null
     */
    private ?Product $product;
    private StockItems $stockItems;
    private Magento $magento;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Product $product = null)
    {
        $this->product = $product;
        $this->magento = new Magento();
        $this->stockItems = new StockItems($this->magento);
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $productsCollection = collect();

        if ($this->product) {
            $productsCollection->add($this->product);
        } else {
            $productsCollection = Product::withAllTags(['CHECK FAILED'])
                ->limit(10)
                ->get();
            Log::info('Selected products for MagentoSync', ['count' => $productsCollection->count()]);
        }

        $productsCollection->each(function (Product $product) {
                $params = [
                    'is_in_stock' => $product->quantity_available > 0,
                    'qty' => $product->quantity_available,
                ];
                $response = $this->stockItems->update($product->sku, $params);

                Log::info('MagentoApi: stockItem updated', [
                    'sku' => $product->sku,
                    'params' => $params,
                    'response_status_code' => $response->status(),
                    'response_body' => $response->json(),
                ]);
        });

        Log::info('Synced product with Magento API', ['count' => $productsCollection->count()]);
    }
}
