<?php

namespace App\Modules\Automations\src\Actions\Order;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Warehouse;
use App\Modules\SplitOrder\src\SplitOrderService;
use Log;

class ShipRemainingProductsAction
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
        Log::debug('Automation Action', [
            'order_number' => $this->event->order->order_number,
            'class' => class_basename(self::class),
            '$options' => $options,
        ]);

        $order = $this->event->order->refresh();

        $order->orderProducts()->each(function (OrderProduct $orderProduct) {
            $orderProduct->quantity_shipped += $orderProduct->quantity_to_ship;
            $orderProduct->save();
        });

        activity()->causedByAnonymous()->performedOn($order)->log('Automatically shipped all products');
    }
}
