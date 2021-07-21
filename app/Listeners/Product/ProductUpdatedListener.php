<?php

namespace App\Listeners\Product;

use App\Models\ProductAlias;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProductUpdatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $product = $event->product;
        ProductAlias::updateOrCreate(
            ['alias' => $product->sku],
            ['product_id' => $product->id]
        );
    }
}
