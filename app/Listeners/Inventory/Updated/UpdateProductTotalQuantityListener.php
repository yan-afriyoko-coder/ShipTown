<?php

namespace App\Listeners\Inventory\Updated;

use App\Events\Inventory\InventoryUpdatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateProductTotalQuantityListener
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
     * @param InventoryUpdatedEvent $event
     * @return void
     */
    public function handle(InventoryUpdatedEvent $event)
    {
        $event->getInventory()
            ->product()
            ->decrement(
                'quantity',
                $event->getInventory()->getOriginal('quantity')
            );

        $event->getInventory()
            ->product()
            ->increment(
                'quantity',
                $event->getInventory()->quantity
            );
    }
}
