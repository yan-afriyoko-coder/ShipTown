<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use App\Modules\Api2cart\src\Services\Api2cartService;
use App\Modules\Api2cart\src\Transformers\ProductTransformer;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class UpdateMissingTypeAndIdJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    /**
     * Execute the job.
     *
     * @throws Exception
     *
     * @return void
     */
    public function handle()
    {
        $collection = Api2cartProductLink::query()
            ->whereNull('api2cart_product_id')
            ->limit(10)
            ->get();

        $collection->each(function (Api2cartProductLink $link) {
            $this->updateTypeAndIdOrCreate($link);
        });

        if ($collection->isNotEmpty()) {
            dispatch(new self());
        }
    }

    /**
     * @param Api2cartProductLink $link
     */
    private function updateTypeAndIdOrCreate(Api2cartProductLink $link): void
    {
        $typeAndId = Api2cartService::getProductTypeAndId(
            $link->api2cartConnection->bridge_api_key,
            $link->product->sku
        );

        $link->update([
            'api2cart_product_type' => $typeAndId['type'],
            'api2cart_product_id' => $typeAndId['id'],
        ]);

        // if product not found by previous search, create new
        if ($link->api2cart_product_id === null) {
            $response = Api2cartService::createSimpleProduct(
                $link->api2cartConnection->bridge_api_key,
                ProductTransformer::toApi2cartPayload($link)
            );

            $link->update([
                'api2cart_product_type' => 'product',
                'api2cart_product_id' => data_get($response->asArray(), 'result.product_id')
            ]);
        }
    }
}
