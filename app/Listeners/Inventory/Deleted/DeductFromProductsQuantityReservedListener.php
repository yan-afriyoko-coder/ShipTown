<?php

namespace App\Listeners\Inventory\Deleted;

use App\Events\Inventory\DeletedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeductFromProductsQuantityReservedListener
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
            'quantity_reserved',
            $event->getInventory()->quantity_reserved
        );
    }
}
