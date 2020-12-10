<?php

namespace App\Observers;

use App\Events\Product\CreatedEvent;
use App\Events\Product\UpdatedEvent;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Products;
use Exception;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    /**
     * Handle the product "created" event.
     *
     * @param Product $product
     * @return void
     */
    public function created(Product $product)
    {
        $warehouse_ids = Warehouse::all('id')
            ->map(function ($warehouse) use ($product) {
                return [
                    'warehouse_id' => $warehouse->getKey(),
                    'product_id' => $product->getKey(),
                    'location_id' => $warehouse->getKey(),
                ];
            });

        Inventory::query()->insert($warehouse_ids->toArray());

        CreatedEvent::dispatch($product);
    }

    /**
     * Handle the product "updated" event.
     *
     * @param Product $product
     * @return void
     * @throws Exception
     */
    public function updated(Product $product)
    {
        UpdatedEvent::dispatch($product);
    }

    /**
     * Handle the product "deleted" event.
     *
     * @param Product $product
     * @return void
     */
    public function deleted(Product $product)
    {
        //
    }

    /**
     * Handle the product "restored" event.
     *
     * @param Product $product
     * @return void
     */
    public function restored(Product $product)
    {
        //
    }

    /**
     * Handle the product "force deleted" event.
     *
     * @param Product $product
     * @return void
     */
    public function forceDeleted(Product $product)
    {
        //
    }
}
