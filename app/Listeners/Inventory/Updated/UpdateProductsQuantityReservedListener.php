<?php

namespace App\Listeners\Inventory\Updated;

use App\Events\Inventory\UpdatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateProductsQuantityReservedListener
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
        $event->getInventory()
            ->product()
            ->decrement(
                'quantity_reserved',
                $event->getInventory()->getOriginal('quantity_reserved')
            );

        $event->getInventory()
            ->product()
            ->increment(
                'quantity_reserved',
                $event->getInventory()->quantity
            );
    }
}
