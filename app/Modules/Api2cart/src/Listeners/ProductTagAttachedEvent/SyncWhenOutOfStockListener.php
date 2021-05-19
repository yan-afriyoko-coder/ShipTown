<?php

namespace App\Modules\Api2cart\src\Listeners\ProductTagAttachedEvent;

use App\Events\Product\TagAttachedEvent;
use App\Modules\Api2cart\src\Jobs\SyncProductJob;

class SyncWhenOutOfStockListener
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
        if ($event->tag() === 'Out Of Stock') {
            $event->product()->log('Product out of stock, forcing sync');
            SyncProductJob::dispatch($event->product());
        }
    }
}
