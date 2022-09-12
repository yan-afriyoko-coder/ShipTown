<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Models\Product;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
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
        $api2cartConnections = Api2cartConnection::all();

        // we want to sync products with smallest quantities first to avoid oversells
        $products = Product::withAllTags(['Available Online', 'Not Synced'])
            ->whereRaw('ID IN (SELECT product_id FROM modules_api2cart_product_links WHERE api2cart_product_id IS NOT NULL)')
            ->orderBy('quantity')
            ->orderBy('updated_at')
            ->get();

        $products->each(function (Product $product) use ($api2cartConnections) {
            $api2cartConnections->each(function (Api2cartConnection $api2cartConnection) use ($product) {
                $this->dispatchSyncJobs($product, $api2cartConnection);
            });
        });

        $this->queueData([
            'total_count' => $products->count(),
        ]);
    }

    /**
     * @param Product $product
     * @param Api2cartConnection $api2cartConnection
     */
    private function dispatchSyncJobs(Product $product, Api2cartConnection $api2cartConnection): void
    {
        $productLink = Api2cartProductLink::firstOrCreate([
            'product_id' => $product->getKey(),
            'api2cart_connection_id' => $api2cartConnection->getKey(),
        ], []);

        SyncProduct::dispatch($productLink);
    }
}
