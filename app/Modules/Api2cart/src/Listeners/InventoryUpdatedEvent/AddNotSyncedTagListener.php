<?php

namespace App\Modules\Api2cart\src\Listeners\InventoryUpdatedEvent;

use App\Events\Inventory\InventoryUpdatedEvent;

class AddNotSyncedTagListener
{
    /**
     * Handle the event.
     *
     * @param InventoryUpdatedEvent $event
     * @return void
     */
    public function handle(InventoryUpdatedEvent $event)
    {
        $product = $event->getInventory()->product;

        if ($product->hasTags(['Available Online'])) {
            $product->attachTag('Not Synced');
        }
    }
}
