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
use function Clue\StreamFilter\fun;

class SyncProductsToApi2Cart implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * @var int
     */
    private int $chunkSize;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->chunkSize = 50;
    }

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

        $chunkSize = $this->chunkSize;

        // we want to sync products with smallest quantities first to avoid oversells
        $query->orderBy('quantity')
            ->orderBy('updated_at')
            ->chunk($chunkSize, function (Collection $products) use ($totalCount, $chunkSize) {
                $this->queueProgressChunk($totalCount, $chunkSize);

                $products->each(function (Product $product) {
                    SyncProductJob::dispatch($product);
                });
            });

        info('Dispatched Api2cart product sync jobs');
    }
}
