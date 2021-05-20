<?php

namespace App\Modules\MagentoApi\src\Listeners\ProductTagAttachedEvent;

use App\Events\Product\ProductTagAttachedEvent;
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
        if (config('modules.magentoApi.enabled') === false) {
            return;
        }

        if ($event->tag() === 'Out Of Stock') {
            $event->product()->log('Product out of stock, forcing MagentoApi sync');
            SyncProductStockJob::dispatch($event->product());
        }
    }
}
