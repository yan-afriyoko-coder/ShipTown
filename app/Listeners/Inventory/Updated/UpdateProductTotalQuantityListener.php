<?php

namespace App\Listeners\Inventory\Updated;

use App\Events\Inventory\UpdatedEvent;
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
     * @param UpdatedEvent $event
     * @return void
     */
    public function handle(UpdatedEvent $event)
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
