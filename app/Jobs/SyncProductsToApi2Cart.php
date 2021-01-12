<?php

namespace App\Jobs;

use App\Jobs\Modules\Api2cart\SyncProductJob;
use App\Models\Product;
use App\Modules\Api2cart\src\Jobs\UpdateOrCreateProductJob;
use App\Modules\Api2cart\src\Jobs\UpdateProductJob;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Carbon\Carbon;
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
        $limit = 100;

        $products = Product::withAllTags(['Available Online', 'Not Synced'])
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get()
            ->each(function (Product $product) {
                SyncProductJob::dispatch($product);
//                $this->dispatchSyncJobs($product);
                $product->detachTag('Not Synced');
            });

        info('Dispatched products to Api2cart', ['count' => $products->count()]);

        // if we got maximum allwed record count, there might be more!
        if($products->count() === $limit) {
            self::dispatch();
        }
    }

    /**
     * @param Product $product
     * @return bool
     */
    private function dispatchSyncJobs(Product $product)
    {
        logger('Dispatching api2cart sync jobs',['sku' => $product->sku]);
        Api2cartConnection::all()
            ->each(function (Api2cartConnection $connection) use ($product) {
                $this->syncProduct($product, $connection);
            });
        logger('Dispatched api2cart sync jobs',['sku' => $product->sku]);
    }

    /**
     * @param Product $product
     * @param Api2cartConnection $connection
     */
    private function syncProduct(Product $product, Api2cartConnection $connection): void
    {
        $product_data = $this->getProductDataIreland($product);

        UpdateOrCreateProductJob::dispatch($connection->bridge_api_key, $product_data);

        logger('Dispatched api2cart sync job Ireland', ['sku' => $product->sku]);

        $product_data = $this->getProductDataUK($product);

        UpdateOrCreateProductJob::dispatch($connection->bridge_api_key, $product_data);

        logger('Dispatched api2cart sync job UK', ['sku' => $product->sku]);
    }

    /**
     * @param $date
     * @return string
     */
    public function formatDateForApi2cart($date): string
    {
        $carbon_date = new Carbon( $date ?? '2000-01-01 00:00:00');

        if ($carbon_date->year < 2000) {
            return '2000-01-01 00:00:00';
        }

        return $date;
    }

    /**
     * @param Product $product
     * @return array
     */
    private function getProductDataIreland(Product $product): array
    {
        $productPrice = $product->prices()
            ->firstOrCreate([
                'product_id' => $product->getKey(),
                'location_id' => 1
            ]);

        $productInventory = $product->inventory()
            ->firstOrCreate([
                'product_id' => $product->getKey(),
                'location_id' => 100
            ]);

        return [
            'product_id' => $product->getKey(),
            'sku' => $product->sku,
            'quantity' => $productInventory->quantity_available ?? 0,
            'in_stock' => $productInventory->quantity_available > 0 ? "True" : "False",
            'price' => $productPrice->price,
            'special_price' => $productPrice->sale_price,
            'sprice_create' => $this->formatDateForApi2cart($productPrice->sale_price_start_date),
            'sprice_expire' => $this->formatDateForApi2cart($productPrice->sale_price_end_date),
            'store_id' => 1,
        ];
    }

    /**
     * @param Product $product
     * @return array
     */
    private function getProductDataUK(Product $product): array
    {
        $productPrice = $product->prices()
            ->firstOrCreate([
                'product_id' => $product->getKey(),
                'location_id' => 5
            ]);

        $productInventory = $product->inventory()
            ->firstOrCreate([
                'product_id' => $product->getKey(),
                'location_id' => 100
            ]);

        return [
            'product_id' => $product->getKey(),
            'sku' => $product->sku,
            'quantity' => $productInventory->quantity_available ?? 0,
            'in_stock' => $productInventory->quantity_available > 0 ? "True" : "False",
            'price' => $productPrice->price,
            'special_price' => $productPrice->sale_price,
            'sprice_create' => $this->formatDateForApi2cart($productPrice->sale_price_start_date),
            'sprice_expire' => $this->formatDateForApi2cart($productPrice->sale_price_end_date),
            'store_id' => 2,
        ];
    }
}
