<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * Class SyncCheckFailedProductsJob.
 */
class SyncCheckFailedProductsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

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

        $this->queueData([
            'total_count' => $query->count(),
        ]);

        $totalCount = $query->count();

        $chunkSize = 100;

        $query->orderBy('quantity')
            ->chunk($chunkSize, function (Collection $products) use ($totalCount, $chunkSize) {
                $this->queueProgressChunk($totalCount, $chunkSize);

                $products->each(function (Product $product) {
                    SyncProductStockJob::dispatch($product);
                });
            });

        $this->queueProgress(0);
        Log::info('Dispatched Sync MagentoApi Jobs', ['count' => $totalCount]);
    }
}
