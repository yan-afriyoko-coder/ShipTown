<?php

namespace App\Modules\Api2cart\src\Listeners;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Illuminate\Support\Arr;

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
        if ($event->inventory->product->doesNotHaveTags(['Available Online'])) {
            return;
        }

        if ($this->warehouseHasRequiredTags($event->inventory->warehouse)) {
            return;
        }

        activity()->withoutLogs(function () use ($event) {
            $event->inventory->product->attachTag('Not Synced');
        });
    }

    /**
     * @param Warehouse $warehouse
     * @return bool
     */
    private function warehouseHasRequiredTags(Warehouse $warehouse): bool
    {
        /** @var Api2cartConnection $api2cartProductLink */
        $api2cartConnection = Api2cartConnection::query()->first();

        $requiredTags = Arr::wrap($api2cartConnection->inventory_source_warehouse_tag);

        return $warehouse->doesNotHaveTags($requiredTags);
    }
}
