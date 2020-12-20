<?php

namespace App\Listeners\Inventory;

use App\Events\Inventory\InventoryUpdatedEvent;

class InventoryUpdatedEventListener
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
        $this->updateProductInventoryTotals($event);
    }

    /**
     * @param InventoryUpdatedEvent $event
     */
    public function updateProductInventoryTotals(InventoryUpdatedEvent $event)
    {
        $inventory = $event->getInventory();
        $product = $inventory->product();

        if ($product) {
            $product->update([
                'quantity' => $product->quantity + $inventory->quantity - $inventory->getOriginal('quantity'),
                'quantity_reserved' => $product->quantity_reserved + $inventory->quantity_reserved - $inventory->getOriginal('quantity_reserved'),
            ]);
        }
    }
}
