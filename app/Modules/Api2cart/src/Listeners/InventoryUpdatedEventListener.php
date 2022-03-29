<?php

namespace App\Modules\Api2cart\src\Listeners;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Models\Inventory;

class InventoryUpdatedEventListener
{
    /**
     * Handle the event.
     *
     * @param InventoryUpdatedEvent $event
     *
     * @return void
     */
    public function handle(InventoryUpdatedEvent $event)
    {
        $product = $event->inventory->product;

        if ($event->inventory->product->hasTags(['Available Online'])
            && $event->inventory->warehouse->hasTags(['magento_stock'])) {
                activity()->withoutLogs(function () use ($product) {
                    $product->attachTag('Not Synced');
                });
        }
    }
}
