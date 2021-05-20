<?php

namespace App\Modules\Api2cart\src\Listeners\ProductTagAttachedEvent;

use App\Events\Product\ProductTagAttachedEvent;
use App\Modules\Api2cart\src\Jobs\SyncProductJob;
use App\Modules\MagentoApi\src\Jobs\SyncProductStockJob;

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
