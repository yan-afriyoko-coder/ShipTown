<?php

namespace App\Modules\Magento2MSI\src\Listeners;

use App\Events\Product\ProductTagAttachedEvent;
use App\Modules\Magento2MSI\src\Models\Magento2msiConnection;
use App\Modules\Magento2MSI\src\Models\Magento2msiProduct;

class ProductTagAttachedEventListener
{
    /**
     * Handle the event.
     *
     *
     * @return void
     */
    public function handle(ProductTagAttachedEvent $event)
    {
        if ($event->tag === 'Available Online') {
            Magento2msiConnection::query()
                ->get()
                ->each(function (Magento2msiConnection $connection) use ($event) {
                    Magento2msiProduct::firstOrCreate([
                        'connection_id' => $connection->getKey(),
                        'product_id' => $event->product->id,
                    ], []);
                });
        }
    }
}
