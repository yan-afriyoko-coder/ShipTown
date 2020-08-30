<?php

namespace App\Listeners\OrderStatusChangedEvent;

use App\Events\OrderStatusChangedEvent;
use App\Models\PickRequest;

class CreatePickRequestsListener
{
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
     * @param  OrderStatusChangedEvent $event
     * @return void
     */
    public function handle(OrderStatusChangedEvent $event)
    {
        if ($event->isStatusCode('picking')) {
            $orderProducts = $event->getOrder()->orderProducts()->get();

            foreach ($orderProducts as $orderProduct) {
                PickRequest::updateOrCreate([
                    'order_product_id' => $orderProduct->getKey(),
                ], [
                    'quantity_required' => $orderProduct->quantity_ordered
                ]);
            }
        }
    }
}
