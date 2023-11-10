<?php

namespace App\Observers;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\Order;
use App\Models\OrderProductShipment;
use App\Models\OrderShipment;
use App\Models\OrderStatus;

class OrderObserver
{
    public function creating(Order $order)
    {
        if ($order->status_code != '') {
            OrderStatus::query()->firstOrCreate(['code' => $order->status_code], ['name' => $order->status_code]);
        }
    }

    public function saving(Order $order)
    {
        if ($order->isAttributeChanged('is_active')) {
            $order->order_closed_at = $order->is_active ? null : now();

            // we want to mark all products as shipped when order is closed
            // this is because have created shipment
            // before packing last few products
            /** @var OrderShipment $last_shipment */
            $last_shipment = OrderShipment::query()->where(['order_id' => $order->getKey()])->latest()->first();

            if ($last_shipment) {
                OrderProductShipment::where(['order_id' => $order->getKey()])
                    ->whereNull('order_shipment_id')
                    ->update(['order_shipment_id' => $last_shipment->getKey()]);
            }
        }
    }

    public function updating(Order $order)
    {
        if ($order->isAttributeChanged('status_code')) {
            OrderStatus::query()->firstOrCreate(['code' => $order->status_code], ['name' => $order->status_code]);
        }
    }

    public function created(Order $order)
    {
        // we will not dispatch CreatedEvent here
        // please use OrderService method
        // CreatedEvent should be dispatched
        // after created OrderProducts etc

        // OrderCreatedEvent::dispatch();
    }

    public function updated(Order $order)
    {
        OrderUpdatedEvent::dispatch($order);
    }
}
