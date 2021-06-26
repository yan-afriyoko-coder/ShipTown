<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Models\Product;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SyncProductsJob implements ShouldQueue
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
        $query = Product::withAllTags(['Available Online', 'Not Synced']);

        $this->queueData([
            'total_count' => $query->count(),
        ]);

        $totalCount = $query->count();

        $chunkSize = 10;

        // we want to sync products with smallest quantities first to avoid oversells
        $query->orderBy('quantity')
            ->orderBy('updated_at')
            ->chunk($chunkSize, function (Collection $products) use ($totalCount, $chunkSize) {
                $this->queueProgressChunk($totalCount, $chunkSize);

                $products->each(function (Product $product) {
                    SyncProductJob::dispatchNow($product);
                });
            });
    }
}
