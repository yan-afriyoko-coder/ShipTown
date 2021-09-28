<?php

namespace App\Modules\Automations\src\Actions\Order;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Warehouse;
use App\Services\OrderService;
use Log;

class SplitOrderToWarehouseCodeAction
{
    /**
    * @var ActiveOrderCheckEvent|OrderCreatedEvent|OrderUpdatedEvent
    */
    private $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * @param $warehouse_code
     */
    public function handle($warehouse_code)
    {
        $warehouse = Warehouse::whereCode($warehouse_code)->first();

        Log::debug('Automation Action', [
            'order_number' => $this->event->order->order_number,
            'class' => class_basename(self::class),
        ]);

        $order = $this->event->order;

        /** @var Order $newOrder */
        $newOrder = Order::where(['order_number' => $this->event->order->order_number . '-' . $warehouse_code])
            ->first();

        $order->orderProducts->each(function (OrderProduct $orderProduct) use ($order, $warehouse, $newOrder) {

            if ($orderProduct->quantity_to_ship === 0.00) {
                return true;
            }

            /** @var Inventory inventory */
            $inventory = Inventory::query()->firstOrCreate([
                'product_id' => $orderProduct->product_id,
                'warehouse_id' => $warehouse->getKey(),
            ], [
                'location_id' => $warehouse->code
            ]);

            if ($inventory->quantity_available > 0) {
                $quantity = min($orderProduct->quantity_to_ship, $inventory->quantity_available);

                $order->is_editing = true;
                $order->save();

                $inventory->quantity_reserved += $quantity;
                $inventory->save();

                $orderProduct->quantity_ordered -= $quantity;
                $orderProduct->save();

                $newOrder = $newOrder ?? $this->replicateOriginalOrder($warehouse->code);

                $newOrderProduct = $orderProduct->replicate();
                $newOrderProduct->quantity_ordered = $quantity;
                $newOrderProduct->order_id = $newOrder->getKey();
                $newOrderProduct->save();
            }
        });

        if ($newOrder) {
            $newOrder->is_editing = false;
            $newOrder->save();
        }

        if ($order->is_editing) {
            $order->is_editing = false;
            $order->save();
        }
    }

    /**
     * @param $warehouse_code
     * @return Order
     */
    private function replicateOriginalOrder($warehouse_code): Order
    {
        $newOrder = $this->event->order->replicate(['is_editing']);
        $newOrder->order_number = $this->event->order->order_number . '-' . $warehouse_code;
        $newOrder->status_code = 'packing_' . $warehouse_code;
        $newOrder->is_editing = true;
        $newOrder->save();

        return $newOrder;
    }
}
