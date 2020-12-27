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

class SyncProductToApi2cart implements ShouldQueue
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
        $product_data = [
            'product_id' => $product->getKey(),
            'sku' => $product->sku,
            'quantity' => $product->quantity_available,
            'in_stock' => $product->quantity_available > 0 ? "True" : "False",
        ];

        Api2cartConnection::all()
            ->each(function (Api2cartConnection $connection) use ($product_data) {
                UpdateProductJob::dispatch($connection->bridge_api_key, $product_data);
            });
    }
}
