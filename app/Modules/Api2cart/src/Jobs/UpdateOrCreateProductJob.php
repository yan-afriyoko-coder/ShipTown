<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Jobs\VerifyProductSyncJob;
use App\Modules\Api2cart\src\Products;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class UpdateOrCreateProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    private $product_data;
    /**
     * @var string
     */
    private $bridge_api_key;

    /**
     * Create a new job instance.
     *
     * @param $bridge_api_key
     * @param $product_data
     */
    public function __construct(string $bridge_api_key, array $product_data)
    {
        $this->bridge_api_key = $bridge_api_key;
        $this->product_data = $product_data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $response = Products::updateOrCreate($this->bridge_api_key, $this->product_data);

            VerifyProductSyncJob::dispatch($this->bridge_api_key, $this->product_data);

            info('Api2cart: Product synced', [
                'product_data' => $this->product_data,
                'response' => $response ? $response->asArray() : null,
            ]);
        } catch (Exception $exception) {
            Log::warning('Api2cart: Product NOT SYNCED, retrying', [
                'product_data' => $this->product_data,
                'exception' => [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ],
            ]);
            $this->release(60);
        }
    }
}
