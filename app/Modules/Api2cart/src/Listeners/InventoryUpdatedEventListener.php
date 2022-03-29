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
        $inventory = $event->inventory;

        if ($inventory->product->doesNotHaveTags(['Available Online'])) {
            return;
        }

        if ($inventory->warehouse->doesNotHaveTags(['magento_stock'])) {
            return;
        }

        activity()->withoutLogs(function () use ($inventory) {
            $inventory->product->attachTag('Not Synced');
        });
    }
}
