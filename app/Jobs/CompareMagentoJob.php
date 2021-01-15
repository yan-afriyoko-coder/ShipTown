<?php

namespace App\Jobs;

use App\Models\Product;
use App\Modules\Api2cart\src\Exceptions\GetRequestException;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Products;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CompareMagentoJob implements ShouldQueue
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
     * @throws GetRequestException
     */
    public function handle()
    {
        Api2cartConnection::all()->each(function(Api2cartConnection $connection) {
            $params = [
                'params' => 'model,price,special_price,quantity,avail_sale,avail_view',
                'count' => '10',
            ];

            $response = Products::getProductList($connection->bridge_api_key, $params);

            $magentoProductList = collect($response->getResult()['product']);

            $magentoProductList->each(function ($magentoProduct) use ($connection) {
                $pmProduct = Product::findBySKU($magentoProduct['u_model']);

                if(is_null($pmProduct)) {
                    \Log::warning('Magento product not found in Products Management', [$magentoProduct]);
                    return;
                }

                if ($this->areNotInSync($magentoProduct, $pmProduct, $connection)) {
                    \Log::warning('Magento product is out of sync', [$pmProduct, $magentoProduct]);
                    return;
                }
            });
        });
    }

    /**
     * @param $magentoProduct
     * @param Model $pmProduct
     * @param Api2cartConnection $connection
     * @return bool
     */
    private function areInSync($magentoProduct, Model $pmProduct, Api2cartConnection $connection): bool
    {
        // compare prices
        // feed prices
        // compare values

        // compare stock
        // feed stock information
        // compare values

        return true;
    }

    /**
     * @param $magentoProduct
     * @param Model $pmProduct
     * @param Api2cartConnection $connection
     * @return bool
     */
    private function areNotInSync($magentoProduct, Model $pmProduct, Api2cartConnection $connection): bool
    {
        return !$this->areInSync($magentoProduct, $pmProduct, $connection);
    }

}
