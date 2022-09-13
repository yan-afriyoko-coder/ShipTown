<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Modules\Api2cart\src\Api\RequestResponse;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use App\Modules\Api2cart\src\Services\Api2cartService;
use App\Modules\Api2cart\src\Transformers\ProductTransformer;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class SyncProductJob.
 */
class SyncVariant implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var Api2cartProductLink
     */
    private Api2cartProductLink $product_link;

    /**
     * Create a new job instance.
     *
     * @param Api2cartProductLink $api2cartProductLink
     */
    public function __construct(Api2cartProductLink $api2cartProductLink)
    {
        $this->product_link = $api2cartProductLink;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws GuzzleException
     */
    public function handle()
    {
        if ($this->product_link->api2cart_product_type != 'variant') {
            return;
        }

        $response = Api2cartService::updateVariant(
            $this->product_link->api2cartConnection->bridge_api_key,
            ProductTransformer::toApi2cartPayload($this->product_link),
        );

        switch ($response->getReturnCode()) {
            case RequestResponse::RETURN_CODE_MODEL_NOT_FOUND:
                // product not found
                $this->product_link->update([
                    'api2cart_product_type' => null,
                    'api2cart_product_id' => null
                ]);
                break;
            case RequestResponse::RETURN_CODE_OK:
                $this->product_link->fetchFromApi2cart();
                $this->product_link->update([
                    'is_in_sync' => null,
                ]);
                break;
        }
    }
}
