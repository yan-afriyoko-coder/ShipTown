<?php

namespace App\Modules\Api2cart\src\Listeners;

use App\Events\Product\ProductTagAttachedEvent;
use App\Modules\Api2cart\src\Jobs\SyncProduct;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;

class ProductTagAttachedEventListener
{
    /**
     * Handle the event.
     *
     * @param ProductTagAttachedEvent $event
     *
     * @return void
     */
    public function handle(ProductTagAttachedEvent $event)
    {
        if ($event->tag() === 'Available Online') {
            Api2cartConnection::query()
                ->get()
                ->each(function (Api2cartConnection $connection) use ($event) {
                    Api2cartProductLink::updateOrCreate([
                        'product_id' => $event->product()->id,
                        'api2cart_connection_id' => $connection->id,
                    ], [
                        'is_in_sync' => null,
                    ]);
                });
        }
    }
}
