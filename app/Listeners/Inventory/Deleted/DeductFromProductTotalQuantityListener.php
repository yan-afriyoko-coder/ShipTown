<?php

namespace App\Listeners\Inventory\Deleted;

use App\Events\Inventory\UpdatedEvent;

class DeductFromProductTotalQuantityListener
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
     * @param UpdatedEvent $event
     * @return void
     */
    public function handle(UpdatedEvent $event)
    {
        $event->getInventory()->product()->decrement(
            'quantity',
            $event->getInventory()->quantity
        );
    }
}
