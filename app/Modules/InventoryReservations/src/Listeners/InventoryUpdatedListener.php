<?php

namespace App\Modules\InventoryReservations\src\Listeners;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Models\Inventory;
use App\Models\Product;

class InventoryUpdatedListener
{
    private Inventory $inventory;
    private Product $product;

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
        $this->inventory = $event->getInventory();
        $this->product = Product::find($this->inventory->product_id);

        $this->recalculateProductTotals();
    }

    /**
     */
    public function recalculateProductTotals(): void
    {
        $this->product->quantity = Inventory::where(['product_id' => $this->inventory->product_id])
            ->where('quantity', '!=', 0)
            ->sum('quantity');
        $this->product->quantity_reserved = Inventory::where(['product_id' => $this->inventory->product_id])
            ->where('quantity_reserved', '!=', 0)
            ->sum('quantity_reserved');
        $this->product->save();
    }
}
