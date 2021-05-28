<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Models\Product;
use App\Modules\Api2cart\src\Jobs\SyncProductJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * Class SyncCheckFailedProductsJob
 * @package App\Jobs
 */
class SyncCheckFailedProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

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
        $this->batchSize = 10;
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
        $query = Product::withAllTags(['CHECK FAILED']);

        $totalCount = $query->count();

        $chunkSize = 10;

        $query->orderBy('quantity')
            ->chunk($chunkSize, function (Collection $products) use ($totalCount, $chunkSize) {
                $this->queueProgressChunk($totalCount, $chunkSize);

                $products->each(function (Product $product) {
                    SyncProductStockJob::dispatch($product);
                });
            });

        Log::info('Dispatched Sync MagentoApi Jobs', ['count' => $totalCount]);
    }
}
