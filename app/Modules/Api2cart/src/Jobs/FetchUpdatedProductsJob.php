<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Modules\Api2cart\src\Api\Products;
use App\Modules\Api2cart\src\Exceptions\RequestException;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class FetchUpdatedProductsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @throws RequestException
     *
     * @return void
     */
    public function handle()
    {
        Api2cartConnection::all()->each(function (Api2cartConnection $connection) {
            $products = $this->fetchProducts($connection);

            $products->each(function ($product) use ($connection) {
                $this->saveApi2cartProduct($connection, $product);
            });
        });
    }

    /**
     * @param Api2cartConnection $connection
     *
     * @throws RequestException
     *
     * @return Collection
     */
    private function fetchProducts(Api2cartConnection $connection): Collection
    {
        $params = [
            'params' => implode(',', [
                'id',
                'model',
                'u_model',
                'sku',
                'u_sku',
                'price',
                'special_price',
                'stores_ids',
                'manage_stock',
                'quantity',
                'inventory',
                'modified_at',
            ]),
            'count'         => 10,
            'sort_by'       => 'modified_at',
            'modified_from' => $this->formatDateForApi2cart(now()->subHours(2)),
            'modified_to'   => $this->formatDateForApi2cart(now()),
        ];

        $response = Products::getProductList($connection->bridge_api_key, $params);

        return collect($response->getResult()['product']);
    }

    /**
     * @param Api2cartConnection $connection
     * @param $product
     */
    private function saveApi2cartProduct(Api2cartConnection $connection, $product): void
    {
        Api2cartProductLink::query()->updateOrCreate([
            'api2cart_connection_id' => $connection->getKey(),
            'api2cart_product_id'    => $product['id'],
        ], [
            'api2cart_product_type' => 'product',
            'last_fetched_at'       => now(),
            'last_fetched_data'     => json_encode($product),
        ]);
    }

    /**
     * @param $date
     *
     * @return string
     */
    private function formatDateForApi2cart($date): string
    {
        $carbon_date = new Carbon($date ?? '2000-01-01 00:00:00');

        if ($carbon_date->year < 2000) {
            return '2000-01-01 00:00:00';
        }

        return $carbon_date->toDateTimeString();
    }
}
