<?php

namespace App\Modules\InventoryMovementsStatistics\src\Listeners;

use App\Events\Product\ProductCreatedEvent;

class ProductCreatedEventListener
{
    public function handle(ProductCreatedEvent $event)
    {
//        InventoryTotal::query()->create([
//            'product_id' => $event->product->getKey(),
//            'quantity' => 0,
//            'quantity_reserved' => 0,
//            'quantity_incoming' => 0,
//        ]);
    }
}
