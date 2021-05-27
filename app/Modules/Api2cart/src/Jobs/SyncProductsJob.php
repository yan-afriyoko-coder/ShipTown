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
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use function Clue\StreamFilter\fun;

class SyncProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $query = Product::withAllTags(['Available Online', 'Not Synced']);

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

        Log::info('Synced products with Api2cart product sync jobs');
    }
}
