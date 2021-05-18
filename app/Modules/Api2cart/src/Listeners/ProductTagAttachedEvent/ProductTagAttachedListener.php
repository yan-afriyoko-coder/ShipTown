<?php

namespace App\Modules\Api2cart\src\Listeners\ProductPriceUpdatedEvent;

use App\Events\Product\TagAttachedEvent;
use App\Modules\Api2cart\src\Jobs\SyncProductJob;

class ProductTagAttachedListener
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
     * @param TagAttachedEvent $event
     * @return void
     */
    public function handle(TagAttachedEvent $event)
    {
        if ($event->tag() !== 'Not Synced') {
            return;
        }

        $product = $event->product();

        if ($product->isOutOfStock()) {
            $product->log('Product out of stock, forcing sync');
            SyncProductJob::dispatch($product);
        }
    }
}
