<?php

namespace App\Listeners\Inventory;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Contracts\Events\Dispatcher;

class UpdateProductQuantity
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen('eloquent.created: App\Models\Inventory',
            'App\Listeners\Inventory\UpdateProductQuantity@onCreated');
        $events->listen('eloquent.updated: App\Models\Inventory',
            'App\Listeners\Inventory\UpdateProductQuantity@onUpdated');
        $events->listen('eloquent.deleted: App\Models\Inventory',
            'App\Listeners\Inventory\UpdateProductQuantity@onDeleted');
    }

    /**
     * @param Inventory $inventory
     */
    public function onCreated(Inventory $inventory)
    {
        $inventory->product()->increment('quantity', $inventory->quantity);
    }

    /**
     * @param Inventory $inventory
     */
    public function onUpdated(Inventory $inventory)
    {
        //TODO deduct old quantity on inventory updated
        //TODO add new quantity on inventory updated
    }

    /**
     * @param Inventory $inventory
     */
    public function onDeleted(Inventory $inventory)
    {
        $inventory->product()->decrement('quantity', $inventory->quantity);
    }
}
