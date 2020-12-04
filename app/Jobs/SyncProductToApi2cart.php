<?php

namespace App\Jobs;

use App\Models\Product;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Products;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
                if ($this->syncProduct($product)) {
                    $product->detachTag('Not Synced');
                }
            });

        info('Synced products to Api2cart', ['count' => $products->count()]);
    }

    /**
     * @param Product $product
     * @return bool
     */
    private function syncProduct(Product $product)
    {
        Api2cartConnection::all()
            ->each(function (Api2cartConnection $connection) use ($product) {
                if (!$this->sync($product, $connection)) {
                    return false;
                };
            });
        return true;
    }

    /**
     * @param Product $product
     * @param Api2cartConnection $connection
     * @return bool
     */
    private function sync(Product $product, Api2cartConnection $connection)
    {
        $product_data = [
            'product_id' => $product->getKey(),
            'sku' => $product->sku,
            'quantity' => $product->quantity_available,
            'in_stock' => $product->quantity_available > 0 ? "True" : "False",
        ];

        Log::debug('Syncing product', $product->toArray());

        try {
            Products::update($connection->bridge_api_key, $product_data);
            return true;
        } catch (Exception $exception) {
            Log::warning('Could not disable product', $product);
            return false;
        }
    }
}
