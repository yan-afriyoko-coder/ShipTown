<?php

namespace App\Modules\MagentoApi\src\Listeners\ProductTagDetachedEvent;

use App\Events\Product\ProductTagDetachedEvent;
use App\Modules\MagentoApi\src\Jobs\SyncProductStockJob;

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
        if (config('modules.magentoApi.enabled') === false) {
            return;
        }

        if ($event->tag() === 'Out Of Stock') {
            $event->product()->log('Product out of stock, forcing MagentoApi sync');
            SyncProductStockJob::dispatch($event->product());
        }
    }
}
