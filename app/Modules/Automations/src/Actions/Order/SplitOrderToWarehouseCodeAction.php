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
     * @param $options
     */
    public function handle($options)
    {
        $optionsSeparated = explode(',', $options);

        $warehouse_code = $optionsSeparated[0];
        $newOrderStatus = $optionsSeparated[1];

        Log::debug('Automation Action', [
            'order_number' => $this->event->order->order_number,
            'class' => class_basename(self::class),
        ]);

        $order = $this->event->order->refresh();

        $warehouse = Warehouse::whereCode($warehouse_code)->first();

        $this->extractFulfillableProducts($order, $warehouse, $newOrderStatus);
    }

    /**
     * @param Order $originalOrder
     * @param Warehouse $warehouse
     * @return Order|null
     */
    public static function extractFulfillableProducts(Order $originalOrder, Warehouse $warehouse, string $newOrderStatus): ?Order
    {
        $newOrder = null;
        $orderProductsToExtract = [];

        $originalOrder->orderProducts->each(
            function (OrderProduct $orderProduct) use ($warehouse, &$orderProductsToExtract, &$originalOrder) {
                if ($orderProduct->quantity_to_ship <= 0) {
                    Log::debug('nothing to ship');
                    return true; // return true to continue loop
                }

                /** @var Inventory $inventory */
                $inventory = $orderProduct->product->inventory()
                    ->where(['warehouse_id' => $warehouse->getKey()])
                    ->first();

                $quantityToExtract = min($inventory->quantity_available, $orderProduct->quantity_to_ship);

                if ($quantityToExtract <= 0.00) {
                    Log::debug('nothing to extract', [
                        $quantityToExtract,$inventory->quantity_available, $orderProduct->quantity_to_ship, $inventory]);
                    return true; // return true to continue loop
                }

                if ($originalOrder->is_editing === false) {
                    $originalOrder->is_editing = true;
                    $originalOrder->save();
                }

                $inventory->quantity_reserved += $quantityToExtract;
                $inventory->save();

                $newOrderProduct = $orderProduct->replicate(['order_id']);
                $newOrderProduct->quantity_ordered = $quantityToExtract;
                $orderProductsToExtract[] = $newOrderProduct;

                $orderProduct->quantity_ordered -= $quantityToExtract;
                $orderProduct->save();
            }
        );

        if ($orderProductsToExtract) {
            $newOrder = $originalOrder->replicate(['status_code']);
            $newOrder->is_editing = true;
            $newOrder->status_code = $newOrderStatus;
            $newOrder->order_number .= '-PARTIAL-' . $warehouse->code;
            $newOrder->save();

            collect($orderProductsToExtract)->each(function (OrderProduct $orderProduct) use ($newOrder) {
                $orderProduct->order()->associate($newOrder);
                $orderProduct->save();
            });
        }

        if ($originalOrder->is_editing) {
            $originalOrder->is_editing = false;
            $originalOrder->save();
        }

        if ($newOrder and $newOrder->is_editing) {
            $newOrder->is_editing = false;
            $newOrder->save();
        }

        return $newOrder;
    }
}
