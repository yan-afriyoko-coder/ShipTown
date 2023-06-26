<?php

namespace App\Modules\InventoryReservations\src\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class DetectAndFixIncorrectQuantityJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $result = Product::query()
            ->whereRaw(/** @lang mysql */  "
                id IN (
                    select
                        products.id

                    from inventory

                    left join products on inventory.product_id = products.id

                    where
                     inventory.quantity <> 0
                     OR products.quantity <> 0

                    group by products.id

                    having max(products.quantity) != sum(inventory.quantity)
                )
                ")
            ->limit(200)
            ->get();

        if ($result->isEmpty()) {
            return;
        }

        Log::warning('Found products with incorrect quantity: ', ['count' => $result->count()]);

        $result->each(function (Product $product) {
            UpdateProductQuantityJob::dispatch($product->getKey());
            UpdateProductQuantityReservedJob::dispatch($product->getKey());
        });
    }
}
