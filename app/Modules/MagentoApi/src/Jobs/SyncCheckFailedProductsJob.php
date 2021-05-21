<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class SyncCheckFailedProductsJob
 * @package App\Jobs
 */
class SyncCheckFailedProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private int $batchSize;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->batchSize = 200;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (config('modules.magentoApi.enabled') === false) {
            return;
        }

        $productsCollection = Product::withAllTags(['CHECK FAILED'])
            ->limit($this->batchSize)
            ->get()
            ->each(function (Product $product) {
                SyncProductStockJob::dispatch($product);
            });

        Log::info('Dispatched Sync MagentoApi Jobs', ['products_count' => $productsCollection->count()]);
    }
}
