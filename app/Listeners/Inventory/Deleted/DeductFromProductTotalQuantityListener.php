<?php

namespace App\Listeners\Inventory\Deleted;

use App\Events\Inventory\DeletedEvent;

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
     * @param DeletedEvent $event
     * @return void
     */
    public function handle(DeletedEvent $event)
    {
        $event->getInventory()->product()->decrement(
            'quantity',
            $event->getInventory()->quantity
        );
    }
}
