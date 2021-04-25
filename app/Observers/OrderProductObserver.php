<?php

namespace App\Observers;

use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Events\OrderProduct\OrderProductUpdatedEvent;
use App\Jobs\Order\RecalculateOrderTotalQuantities;
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
        OrderProductCreatedEvent::dispatch($orderProduct);

        RecalculateOrderTotalQuantities::dispatchNow($orderProduct->order()->first());

        $this->recalculateQuantityReserved($orderProduct);
    }

    /**
     * Handle the order product "updated" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function updated(OrderProduct $orderProduct)
    {
        OrderProductUpdatedEvent::dispatch($orderProduct);

        RecalculateOrderTotalQuantities::dispatchNow($orderProduct->order()->first());

        $this->recalculateQuantityReserved($orderProduct);

        $this->setOrdersPickedAtIfAllPicked($orderProduct);
    }

    /**
     * Handle the order product "deleted" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function deleted(OrderProduct $orderProduct)
    {
        RecalculateOrderTotalQuantities::dispatchNow($orderProduct->order()->first());

        $this->recalculateQuantityReserved($orderProduct);
    }

    /**
     * Handle the order product "restored" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function restored(OrderProduct $orderProduct)
    {
        RecalculateOrderTotalQuantities::dispatchNow($orderProduct->order()->first());

        $this->recalculateQuantityReserved($orderProduct);
    }

    /**
     * Handle the order product "force deleted" event.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function forceDeleted(OrderProduct $orderProduct)
    {
        RecalculateOrderTotalQuantities::dispatchNow($orderProduct->order()->first());

        $this->recalculateQuantityReserved($orderProduct);
    }

    /**
     * @param OrderProduct $orderProduct
     */
    private function setOrdersPickedAtIfAllPicked(OrderProduct $orderProduct): void
    {
        $orderHasMoreToPick = OrderProduct::query()
            ->where('quantity_to_pick', '>', 0)
            ->where(['order_id' => $orderProduct->order_id])
            ->exists();

        if ($orderHasMoreToPick === false) {
            $orderProduct->order()->update(['picked_at' => now()]);
        }
    }

    /**
     * @param OrderProduct $orderProduct
     */
    public function recalculateQuantityReserved(OrderProduct $orderProduct): void
    {
        if ($orderProduct->product) {
            $inventory = Inventory::firstOrNew([
                'product_id' => $orderProduct->product_id,
                'location_id' => 999,
            ]);

            $inventory->quantity_reserved = OrderProduct::where(['product_id' => $orderProduct->product_id])
                ->where('quantity_reserved', '!=', 0)
                ->sum('quantity_reserved');
            $inventory->save();
        }
    }
}
