<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Models\Product;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncProductsToApi2Cart implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $this->chunkSize = 200;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $products = Product::withAllTags(['Available Online', 'Not Synced'])
            // we want to sync products with smallest quantities first to avoid oversells
            ->orderBy('quantity')
            ->orderBy('updated_at')
            ->limit($this->chunkSize)
            ->get();

        $products->each(function ($product) {
            SyncProductJob::dispatch($product);
        });

        info('Dispatched Api2cart product sync jobs', ['count' => $products->count()]);
    }
}
