<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Modules\Api2cart\src\Exceptions\RequestException;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartSimpleProduct;
use App\Modules\Api2cart\src\Services\Api2cartService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchSimpleProductsInfoJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     * @throws GuzzleException
     * @throws RequestException
     */
    public function handle()
    {
        Api2cartConnection::query()
            ->get()
            ->each(function (Api2cartConnection $api2cartConnection) {
                $product_ids = Api2cartSimpleProduct::query()
                    ->where(['api2cart_connection_id' => $api2cartConnection->id])
                    ->whereNull('last_fetched_data')
                    ->orderBy('updated_at')
                    ->limit(10)
                    ->pluck('api2cart_product_id');

                if ($product_ids->isEmpty()) {
                    return;
                }

                $response = Api2cartService::getProductsList($api2cartConnection, $product_ids->toArray());

                if ($response->isNotSuccess()) {
                    throw new RequestException(implode(' ', [
                            $response->getReturnCode(),
                            $response->getReturnMessage()
                        ]));
                }

                $productRecords = data_get($response->collect(), 'result.product');

                collect($productRecords)
                    ->each(function ($product) use ($api2cartConnection) {
                        $productLink = Api2cartSimpleProduct::query()
                            ->where([
                                'api2cart_product_id' => $product['id'],
                                'api2cart_connection_id' => $api2cartConnection->id,
                            ])->first();

                        $productLink->update([
                            'is_in_sync' => null,
                            'last_fetched_data' => Api2cartService::transformProduct($product, $api2cartConnection)
                        ]);
                    });
            });
    }
}
