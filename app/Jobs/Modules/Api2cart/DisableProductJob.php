<?php

namespace App\Jobs\Modules\Api2cart;

use App\Models\Product;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Products;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DisableProductJob implements ShouldQueue
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
