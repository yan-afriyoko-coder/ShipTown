<?php

namespace App\Modules\AutoTags\src\Listeners\InventoryUpdatedEvent;

use App\Events\Inventory\InventoryUpdatedEvent;

class AttachOutOfStockTagListener
{
    /**
     * Handle the event.
     *
     * @param InventoryUpdatedEvent $event
     * @return void
     */
    public function handle(InventoryUpdatedEvent $event)
    {
        $product = $event->getInventory()->product;

        if ($product->isOutOfStock()) {
            $product->attachTag('Out Of Stock');
        }
    }
}
