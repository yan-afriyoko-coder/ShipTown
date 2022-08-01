<?php

namespace App\Modules\InventoryReservations\src\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 *
 */
class DetectAndFixIncorrectQuantityJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private string $table_name;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->dispatchFixForOutOfStockStock();
        $this->dispatchFixForInStock();
    }

    private function dispatchFixForOutOfStockStock(): void
    {
        $rawQuery = /** @lang mysql */ "
            select
               products.id as product_id

            from products

            left join inventory on inventory.product_id = products.id
              and inventory.quantity <> 0

            where
             products.quantity = 0
             AND inventory.id IS NOT NULL

            group by products.id

            having max(products.quantity) <> sum(inventory.quantity)
        ";

        $this->dispatRecalculateJobsForProductIdIn($rawQuery);
    }

    private function dispatchFixForInStock(): void
    {
        $rawQuery = /** @lang mysql */ "
                select
                    products.id

                from products

                left join inventory on inventory.product_id = products.id

                where
                 products.quantity > 0
                 AND inventory.quantity <> 0

                group by products.id

                having sum(inventory.quantity) != max(products.quantity)
        ";

        $this->dispatRecalculateJobsForProductIdIn($rawQuery);
    }

    /**
     * @param string $rawQuery
     */
    private function dispatRecalculateJobsForProductIdIn(string $rawQuery): void
    {
        $query = Product::query()
            ->whereRaw("products.id IN ({$rawQuery})")
            ->limit(50);

        $result = $query->get();

        while ($result->count() > 0) {
            $result->each(function (Product $product) {
                UpdateProductQuantityJob::dispatch($product->getKey());
                UpdateProductQuantityReservedJob::dispatch($product->getKey());
            });

            $result = $query->get();
        }
    }
}
