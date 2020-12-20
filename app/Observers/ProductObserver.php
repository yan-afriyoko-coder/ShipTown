<?php

namespace App\Observers;

use App\Events\Product\ProductCreatedEvent;
use App\Events\Product\ProductUpdatedEvent;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Exception;

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
        ProductCreatedEvent::dispatch($product);
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
        ProductUpdatedEvent::dispatch($product);
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
