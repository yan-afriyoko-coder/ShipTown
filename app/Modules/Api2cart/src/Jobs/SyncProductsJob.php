<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Models\Product;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use App\Modules\MagentoApi\src\Jobs\SyncProductStockJob;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
        $connections = Api2cartConnection::all();

        $query = Product::withAllTags(['Available Online', 'Not Synced']);

        $this->queueData([
            'total_count' => $query->count(),
        ]);

        // we want to sync products with smallest quantities first to avoid oversells
        $products = $query->orderBy('quantity')
            ->orderBy('updated_at')
            ->get();

        $products->each(function (Product $product) use ($connections) {
            $connections->each(function (Api2cartConnection $api2cartConnection) use ($product) {
                    Api2cartProductLink::firstOrCreate([
                            'product_id' => $product->getKey(),
                            'api2cart_connection_id' => $api2cartConnection->getKey(),
                        ], [])
                        ->each(function (Api2cartProductLink $product_link) {
                            SyncProduct::dispatch($product_link);
                            SyncProductStockJob::dispatch($product_link->product);
                        });
            });
        });
    }
}
