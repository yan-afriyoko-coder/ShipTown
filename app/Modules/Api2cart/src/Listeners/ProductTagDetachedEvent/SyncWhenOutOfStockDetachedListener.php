<?php

namespace App\Modules\Api2cart\src\Listeners\ProductTagDetachedEvent;

use App\Events\Product\ProductTagDetachedEvent;
use App\Modules\Api2cart\src\Jobs\SyncProductJob;

class SyncWhenOutOfStockDetachedListener
{
    /**
     * Handle the event.
     *
     * @param ProductTagDetachedEvent $event
     * @return void
     */
    public function handle(ProductTagDetachedEvent $event)
    {
        if ($event->tag() !== 'Out Of Stock') {
            return;
        }

        if ($event->product()->doesNotHaveTags(['Available Online'])) {
            return;
        }

        $event->product()->log('Product out of stock, forcing sync');
        SyncProductJob::dispatch($event->product());
    }
}
