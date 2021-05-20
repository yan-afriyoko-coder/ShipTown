<?php

namespace App\Modules\Api2cart\src\Listeners\ProductTagAttachedEvent;

use App\Events\Product\ProductTagAttachedEvent;
use App\Modules\Api2cart\src\Jobs\SyncProductJob;

class SyncWhenOutOfStockAttachedListener
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
     * @param ProductTagAttachedEvent $event
     * @return void
     */
    public function handle(ProductTagAttachedEvent $event)
    {
        if ($event->tag() === 'Out Of Stock') {
            $event->product()->log('Product out of stock, forcing sync');
            SyncProductJob::dispatch($event->product());
        }
    }
}
