<?php

namespace App\Jobs;

use App\Jobs\Modules\Api2cart\SyncProductJob;
use App\Models\Product;
use App\Modules\Api2cart\src\Jobs\UpdateOrCreateProductJob;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Carbon\Carbon;
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
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $limit = 100;

        $products = Product::withAllTags(['Available Online', 'Not Synced'])
            ->orderBy('updated_at')
            ->limit($limit)
            ->get()
            ->each(function (Product $product) {
                SyncProductJob::dispatch($product);
                $product->detachTag('Not Synced');
                logger('SyncProductJob dispatched and tag removed', ['sku' => $product->sku]);
            });

        info('Dispatched Api2cart product sync jobs', ['count' => $products->count()]);

        // if we got maximum allowed record count, there might be more!
        if($products->count() === $limit) {
            self::dispatch();
        }
    }
}
