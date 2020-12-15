<?php

namespace App\Observers;

use App\Events\Inventory\CreatedEvent;
use App\Events\Inventory\DeletedEvent;
use App\Events\Inventory\UpdatedEvent;
use App\Models\Inventory;

class InventoryObserver
{
    /**
     * Handle the inventory "created" event.
     *
     * @param Inventory $inventory
     * @return void
     */
    public function created(Inventory $inventory)
    {
        $product = $inventory->product();

        if ($product) {
            $product->update([
                'quantity' => $product->quantity + $inventory->quantity,
                'quantity_reserved' => $product->quantity_reserved + $inventory->quantity_reserved
            ]);
        }

        CreatedEvent::dispatch($inventory);
    }

    /**
     * Handle the inventory "updated" event.
     *
     * @param Inventory $inventory
     * @return void
     */
    public function updated(Inventory $inventory)
    {
        $product = $inventory->product();

        if ($product) {
            $product->update([
                'quantity' => $product->quantity + $inventory->quantity - $inventory->getOriginal('quantity'),
                'quantity_reserved' => $product->quantity_reserved + $inventory->quantity_reserved - $inventory->getOriginal('quantity_reserved'),
            ]);
        }

        UpdatedEvent::dispatch($inventory);
    }

    /**
     * Handle the inventory "deleted" event.
     *
     * @param Inventory $inventory
     * @return void
     */
    public function deleted(Inventory $inventory)
    {
        $product = $inventory->product();

        if ($product) {
            $product->update([
                'quantity' => $product->quantity - $inventory->getOriginal('quantity'),
                'quantity_reserved' => $product->quantity_reserved - $inventory->getOriginal('quantity_reserved'),
            ]);
        }

        DeletedEvent::dispatch($inventory);
    }

    /**
     * Handle the inventory "restored" event.
     *
     * @param Inventory $inventory
     * @return void
     */
    public function restored(Inventory $inventory)
    {
        $product = $inventory->product();

        if ($product) {
            $product->update([
                'quantity' => $product->quantity + $inventory->quantity,
                'quantity_reserved' => $product->quantity_reserved + $inventory->quantity_reserved
            ]);
        }
    }

    /**
     * Handle the inventory "force deleted" event.
     *
     * @param Inventory $inventory
     * @return void
     */
    public function forceDeleted(Inventory $inventory)
    {
        $product = $inventory->product();

        if ($product) {
            $product->update([
                'quantity' => $product->quantity - $inventory->getOriginal('quantity'),
                'quantity_reserved' => $product->quantity_reserved - $inventory->getOriginal('quantity_reserved'),
            ]);
        }
    }
}
