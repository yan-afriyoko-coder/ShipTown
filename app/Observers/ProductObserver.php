<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\RmsapiConnection;
use App\Models\Warehouse;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Illuminate\Support\Arr;

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
            ->map(function ($warehouse) use ($product){
                return [
                    'warehouse_id' => $warehouse->getKey(),
                    'product_id' => $product->getKey(),
                    'location_id' => $warehouse->getKey(),
                ];
            });


        Inventory::query()->insert($warehouse_ids->toArray());
    }

    /**
     * Handle the product "updated" event.
     *
     * @param Product $product
     * @return void
     */
    public function updated(Product $product)
    {
        //
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
