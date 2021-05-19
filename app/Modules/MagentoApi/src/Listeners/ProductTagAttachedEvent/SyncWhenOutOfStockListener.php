<?php

namespace App\Modules\MagentoApi\src\Listeners\ProductTagAttachedEvent;

use App\Events\Product\TagAttachedEvent;
use App\Modules\MagentoApi\src\Jobs\SyncProductStockJob;

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
        if (config('modules.magentoApi.enabled') === false) {
            return;
        }

        if ($event->tag() === 'Out Of Stock') {
            $event->product()->log('Product out of stock, forcing MagentoApi sync');
            SyncProductStockJob::dispatch($event->product());
        }
    }
}
