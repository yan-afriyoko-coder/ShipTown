<?php

namespace App\Modules\InventoryTotals\src\Listeners;

use App\Events\Product\ProductCreatedEvent;
use App\Models\InventoryTotal;

class ProductCreatedEventListener
{
    public function handle(ProductCreatedEvent $event)
    {
        InventoryTotal::query()->create([
            'product_id' => $event->product->getKey(),
            'quantity' => 0,
            'quantity_reserved' => 0,
            'quantity_incoming' => 0,
        ]);
    }
}
