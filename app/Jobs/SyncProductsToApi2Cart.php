<?php

namespace App\Jobs;

use App\Models\Product;
use App\Modules\Api2cart\src\Jobs\UpdateProductJob;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncProductsToApi2Cart implements ShouldQueue
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
     * @throws Exception
     */
    public function handle()
    {
        $products = Product::withAllTags(['Available Online', 'Not Synced'])
            ->get()
            ->each(function (Product $product) {
                $this->dispatchSyncJobs($product);
                $product->detachTag('Not Synced');
            });

        info('Synced products to Api2cart', ['count' => $products->count()]);
    }

    /**
     * @param Product $product
     * @return bool
     */
    private function dispatchSyncJobs(Product $product)
    {
        Api2cartConnection::all()
            ->each(function (Api2cartConnection $connection) use ($product) {
                $this->syncProduct($product, $connection);
            });
    }

    /**
     * @param Product $product
     * @param Api2cartConnection $connection
     */
    private function syncProduct(Product $product, Api2cartConnection $connection): void
    {
        $productPrice = $product->prices()->where('location_id', 1)->first();

        $product_data = [
            'product_id' => $product->getKey(),
            'sku' => $product->sku,
            'quantity' => $product->inventory()->where('location_id', 100)->first()->quantity_available,
            'in_stock' => $product->quantity_available > 0 ? "True" : "False",
            'special_price' => $productPrice->sale_price,
            'sprice_create' => $productPrice->sale_price_start_date,
            'sprice_expire' => $productPrice->sale_price_end_date,
            'store_id' => 1,
        ];

        UpdateProductJob::dispatch($connection->bridge_api_key, $product_data);
    }
}
