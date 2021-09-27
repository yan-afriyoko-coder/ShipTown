<?php

namespace App\Modules\Automations\src\Actions\Order;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Models\Order;
use App\Models\OrderProduct;
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
        Log::debug('Automation Action', [
            'order_number' => $this->event->order->order_number,
            'class' => class_basename(self::class),
        ]);

        $order = $this->event->order;

        /** @var Order $newOrder */
        $newOrder = null;

        $order->orderProducts->each(function (OrderProduct $orderProduct) use ($order, $warehouse_code, $newOrder) {
            if (OrderService::canFulfillOrderProduct($orderProduct, $warehouse_code)) {
                $order->is_editing = true;
                $order->save();

                if ($newOrder === null) {
                    $newOrder = $this->replicateOriginalOrder($warehouse_code);
                }

                $newOrderProduct = $orderProduct->replicate();
                $newOrderProduct->order_id = $newOrder->getKey();
                $newOrderProduct->save();

                $orderProduct->delete();
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
