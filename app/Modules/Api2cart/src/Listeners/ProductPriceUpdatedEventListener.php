<?php

namespace App\Modules\Api2cart\src\Listeners;

use App\Events\Product\ProductPriceUpdatedEvent;

class ProductPriceUpdatedEventListener
{
    /**
     * Handle the event.
     *
     * @param ProductPriceUpdatedEvent $event
     *
     * @return void
     */
    public function handle(ProductPriceUpdatedEvent $event)
    {
        $product_price = $event->product_price;

        if ($product_price->product->doesNotHaveTags(['Available Online'])) {
            return;
        }

        if ($product_price->warehouse->doesNotHaveTags(['magento_stock'])) {
            return;
        }

        activity()->withoutLogs(function () use ($product_price) {
            $product_price->product->attachTag('Not Synced');
        });
    }
}
