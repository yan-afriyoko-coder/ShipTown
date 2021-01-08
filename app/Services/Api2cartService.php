<?php

namespace App\Services;

use App\Models\Product;
use App\Modules\Api2cart\src\Jobs\UpdateOrCreateProductJob;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Products;
use Exception;
use Illuminate\Support\Facades\Log;

class Api2cartService
{
    /**
     * @param Product $product
     * @throws Exception
     */
    public static function syncProduct(Product $product): void
    {
        Api2cartConnection::all()
            ->each(function ($connection) use ($product) {
                UpdateOrCreateProductJob::dispatch();
                self::updateProduct($product, $connection->bridge_api_key);
            });
    }

    /**
     * @param Product $product
     * @param $bridge_api_key
     */
    private static function updateProduct(Product $product, $bridge_api_key): void
    {
        try {
            $product_data = [
                'product_id' => $product->getKey(),
                'sku' => $product->sku,
                'quantity' => 0,
                'in_stock' => "False",
            ];

            $requestResponse = Products::update($bridge_api_key, $product_data);

            if ($requestResponse && $requestResponse->isSuccess()) {
                $product->log('Product quantity set to 0 on website');
            }
        } catch (Exception $exception) {
            $product->log('Could not set quantity to 0 on website');
            Log::error('Could not set product quantity to 0 on website', [
                'message' => $exception->getMessage()
            ]);
        }
    }
}
