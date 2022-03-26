<?php

namespace App\Modules\Api2cart\src\Listeners;

use App\Events\Product\ProductTagDetachedEvent;
use App\Modules\Api2cart\src\Jobs\SyncProduct;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;

class ProductTagDetachedEventListener
{
    /**
     * Handle the event.
     *
     * @param ProductTagDetachedEvent $event
     *
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

        Api2cartProductLink::where(['product_id' => $event->product()->getKey()])
            ->each(function (Api2cartProductLink $product_link) {
                SyncProduct::dispatch($product_link);
            });
    }
}
