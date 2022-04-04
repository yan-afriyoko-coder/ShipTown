<?php

namespace App\Modules\Api2cart\src\Listeners;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Modules\Api2cart\src\Models\Api2cartConnection;

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

        /** @var Api2cartConnection $api2cartProductLink */
        $api2cartConnection = Api2cartConnection::query()->first();

        if ($inventory->warehouse->doesNotHaveTags($api2cartConnection->inventory_source_warehouse_tag)) {
            return;
        }

        activity()->withoutLogs(function () use ($inventory) {
            $inventory->product->attachTag('Not Synced');
        });
    }
}
