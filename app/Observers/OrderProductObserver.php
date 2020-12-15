<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\OrderProduct;

class OrderProductObserver
{
    /**
     * Handle the order product "created" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function created(OrderProduct $orderProduct)
    {
        if ($orderProduct->product_id) {
            $inventory = Inventory::firstOrCreate([
                'product_id' => $orderProduct->product_id,
                'location_id' => 999
            ]);

            $inventory->update([
                'quantity_reserved' => $inventory->quantity_reserved + $orderProduct->quantity_to_ship
            ]);
        }


        $orderProduct->order()->increment('total_quantity_ordered', $orderProduct['quantity_ordered']);
        $orderProduct->order()->increment('product_line_count');
    }

    /**
     * Handle the order product "updated" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function updated(OrderProduct $orderProduct)
    {
        if ($orderProduct->product_id) {
            $inventory = Inventory::firstOrCreate([
                'product_id' => $orderProduct->product_id,
                'location_id' => 999
            ]);

            $inventory->update([
                'quantity_reserved' => $inventory->quantity_reserved - $orderProduct->getOriginal('quantity_to_ship') + $orderProduct->quantity_to_ship
            ]);
        }

        $quantity_delta = $orderProduct['quantity_ordered'] - $orderProduct->getOriginal('quantity_ordered');
        $orderProduct->order()->increment('total_quantity_ordered', $quantity_delta);

        $orderHasMoreToPick = OrderProduct::query()
            ->where('quantity_to_pick', '>', 0)
            ->where(['order_id' => $orderProduct->order_id])
            ->exists();

        if ($orderHasMoreToPick === false) {
            $orderProduct->order()->update(['picked_at' => now()]);
        }
    }

    /**
     * Handle the order product "deleted" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function deleted(OrderProduct $orderProduct)
    {
        if ($orderProduct->product_id) {
            $inventory = Inventory::firstOrCreate([
                'product_id' => $orderProduct->product_id,
                'location_id' => 999
            ]);

            $inventory->update([
                'quantity_reserved' => $inventory->quantity_reserved - $orderProduct->quantity_to_ship
            ]);
        }


        $orderProduct->order()->decrement('total_quantity_ordered', $orderProduct['quantity_ordered']);
        $orderProduct->order()->decrement('product_line_count');
    }

    /**
     * Handle the order product "restored" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function restored(OrderProduct $orderProduct)
    {
        if ($orderProduct->product_id) {
            $inventory = Inventory::firstOrCreate([
                'product_id' => $orderProduct->product_id,
                'location_id' => 999
            ]);

            $inventory->update([
                'quantity_reserved' => $inventory->quantity_reserved + $orderProduct->quantity_to_ship
            ]);
        }

        $orderProduct->order()->increment('total_quantity_ordered', $orderProduct['quantity_ordered']);
        $orderProduct->order()->increment('product_line_count');
    }

    /**
     * Handle the order product "force deleted" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function forceDeleted(OrderProduct $orderProduct)
    {
        if ($orderProduct->product_id) {
            $inventory = Inventory::firstOrCreate([
                'product_id' => $orderProduct->product_id,
                'location_id' => 999
            ]);

            $inventory->update([
                'quantity_reserved' => $inventory->quantity_reserved - $orderProduct->quantity_to_ship
            ]);
        }

        $orderProduct->order()->decrement('total_quantity_ordered', $orderProduct['quantity_ordered']);
        $orderProduct->order()->decrement('product_line_count');
    }
}
