<?php

namespace App\Jobs\Modules\Api2cart;

use App\Models\Product;
use App\Modules\Api2cart\src\Jobs\UpdateOrCreateProductJob;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Products;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Api2cartConnection[]|Collection
     */
    private $connections;
    /**
     * @var Product
     */
    private $product;

    /**
     * Create a new job instance.
     *
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
        $this->connections = Api2cartConnection::all();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $product = $this->product;
        $this->connections->each(function ($connection) use ($product) {
            $product_data = array_merge(
                 $this->getBasicData($product),
                // getPricingData($connection->pricingLocationId = 1)
                // getInventoryData($connection->inventoryLocationId = 100)
            );

            self::dispatchSyncJobs($product, $connection->bridge_api_key);
        });
    }


    /**
     * @param Product $product
     * @param string|null $store_key
     */
    private function dispatchSyncJobs(Product $product, string $store_key = null)
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

    /**
     * @param Product $product
     * @return array
     */
    private function getBasicData(Product $product): array
    {
        return [
            'product_id' => $product->getKey(),
            'sku' => $product->sku
        ];
    }
}
