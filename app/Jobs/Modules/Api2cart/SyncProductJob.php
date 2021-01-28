<?php

namespace App\Jobs\Modules\Api2cart;

use App\Jobs\VerifyProductSyncJob;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Products;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use function Clue\StreamFilter\fun;

class SyncProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $product = $this->product;
        Api2cartConnection::all()->each(function ($connection) use ($product) {
            try {
                $product_data = $this->getProductData($product, $connection);

                $response = Products::updateOrCreate($connection->bridge_api_key, $product_data);

                VerifyProductSyncJob::dispatch($connection->bridge_api_key, $product_data);

                info('Api2cart: Product synced', [
                    'response' => $response ? $response->asArray() : null,
                    'product_data' => $product_data,
                ]);
            } catch (Exception $exception) {

                retry(20, function () use ($product) {
                    $product->attachTag('Not Synced');
                }, 100);

                Log::warning('Api2cart: Product NOT SYNCED', [
                    'response' => [
                        'code' => $exception->getCode(),
                        'message' => $exception->getMessage(),
                    ],
                    'product_data' => $product_data,
                ]);
            }
        });


    }

    /**
     * @param Product $product
     * @return array
     */
    private function getBasicData(Product $product): array
    {
        return [
            'product_id' => $product->getKey(),
            'sku' => $product->sku,
            'name' => $product->name,
        ];
    }

    /**
     * @param Product $product
     * @param int $location_id
     * @return array
     */
    private function getPricingData(Product $product, int $location_id): array
    {
        $attributes = [
            'product_id' => $product->getKey(),
            'location_id' => $location_id
        ];

        $productPrice = ProductPrice::query()->where($attributes)->first();

        if($productPrice) {
            return [
                'price' => $productPrice->price,
                'special_price' => $productPrice->sale_price,
                'sprice_create' => $this->formatDateForApi2cart($productPrice->sale_price_start_date),
                'sprice_expire' => $this->formatDateForApi2cart($productPrice->sale_price_end_date),
            ];
        }

        Log::warning('Pricing data not found', $attributes);
        return [];
    }

    /**
     * @param Product $product
     * @param int|null $location_id
     * @return array
     */
    private function getInventoryData(Product $product, int $location_id = null): array
    {
        if(is_null($location_id)) {
            // we will refresh to get latest data
            $product = $product->refresh();

            return [
                'quantity' => $product->quantity_available ?? 0,
                'in_stock' => $product->quantity_available > 0 ? "True" : "False",
            ];
        }

        $attributes = [
            'product_id' => $product->getKey(),
            'location_id' => $location_id
        ];

        $productInventory = Inventory::query()->where($attributes)->first();

        if($productInventory) {
            return [
                'quantity' => $productInventory->quantity_available ?? 0,
                'in_stock' => $productInventory->quantity_available > 0 ? "True" : "False",
            ];
        }

        Log::warning('Inventory data not found', $attributes);
        return [];
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

        return $carbon_date->toDateTimeString();
    }

    /**
     * @param $connection
     * @return array
     */
    private function getMagentoStoreId($connection): array
    {
        if(empty($connection->magento_store_id)) {
            Log::warning('Magento store id not specified!', $connection->getAttributes());
        }

        return [
            'store_id' => $connection->magento_store_id
        ];

    }

    /**
     * @param Product $product
     * @param $connection
     * @return array
     */
    private function getProductData(Product $product, $connection): array
    {
        return array_merge(
            $this->getBasicData($product),
            $this->getPricingData($product, $connection->pricing_location_id),
            $this->getInventoryData($product, $connection->inventory_location_id),
            $this->getMagentoStoreId($connection)
        );
    }
}
