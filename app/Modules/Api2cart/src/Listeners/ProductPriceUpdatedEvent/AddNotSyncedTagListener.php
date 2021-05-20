<?php

namespace App\Modules\Api2cart\src\Listeners\ProductPriceUpdatedEvent;

use App\Events\ProductPrice\ProductPriceUpdatedEvent;

class AddNotSyncedTagListener
{
    /**
     * Handle the event.
     *
     * @param ProductPriceUpdatedEvent $event
     * @return void
     */
    public function handle(ProductPriceUpdatedEvent $event)
    {
        $product = $event->getProductPrice()->product;

        if ($product->hasTags(['Available Online'])) {
            $product->attachTag('Not Synced');
        }
    }
}
