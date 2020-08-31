<?php

namespace App\Listeners\Order\StatusChanged;

use App\Events\Order\StatusChangedEvent;
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
     * @param StatusChangedEvent $event
     * @return void
     */
    public function handle(StatusChangedEvent $event)
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
