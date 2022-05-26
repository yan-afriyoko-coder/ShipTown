<?php

namespace App\Observers;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\Order;
use App\Models\OrderProductShipment;
use App\Models\OrderShipment;
use App\Models\OrderStatus;
use Illuminate\Support\Facades\DB;

class OrderObserver
{
    /**
     * Handle the order "created" event.
     *
     * @param Order $order
     *
     * @return void
     */
    public function created(Order $order)
    {
        // we will not dispatch CreatedEvent here
        // please use OrderService method
        // CreatedEvent should be dispatched
        // after created OrderProducts etc
    }

    public function saving(Order $order)
    {
        $order->total_products = $order->orderTotals ? $order->orderTotals->total_ordered : 0;
        $order->total = $order->total_products + $order->total_shipping;

        $order->is_active = $order->order_status->order_active ?? 1;

        if (($order->exists === false) || ($order->isAttributeChanged('status_code'))) {
            OrderStatus::firstOrCreate([
                'code' => $order->status_code
            ], [
                'name' => $order->status_code
            ]);
        }

        if ($order->isAttributeChanged('is_active')) {
            $order->order_closed_at = $order->is_active ? null : now();

            /** @var OrderShipment $last_shipment */
            $last_shipment = OrderShipment::query()->where(['order_id' => $order->getKey()])->latest()->first();

            if ($last_shipment) {
                OrderProductShipment::where(['order_id' => $order->getKey()])
                    ->whereNull('order_shipment_id')
                    ->update(['order_shipment_id' => $last_shipment->getKey()]);
            }
        }
    }

    /**
     * Handle the order "updated" event.
     *
     * @param Order $order
     *
     * @return void
     */
    public function updated(Order $order)
    {
        OrderUpdatedEvent::dispatch($order);
    }
}
