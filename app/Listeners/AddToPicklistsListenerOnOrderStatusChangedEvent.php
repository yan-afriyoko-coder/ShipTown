<?php

namespace App\Listeners;

use App\Events\OrderStatusChangedEvent;
use App\Models\Picklist;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddToPicklistsListenerOnOrderStatusChangedEvent
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
     * @param  OrderStatusChangedEvent  $event
     * @return void
     */
    public function handle(OrderStatusChangedEvent $event)
    {
        if ($event->order->status_code == 'picking') {

            foreach ($event->order->orderProducts()->get() as $orderProduct) {
                Picklist::query()->create([
                    'product_id' => $orderProduct->product_id,
                    'location_id' => 'WWW',
                    'sku_ordered' => $orderProduct->sku_ordered,
                    'name_ordered' => $orderProduct->name_ordered,
                    'quantity_to_pick' => $orderProduct->quantity,
                ]);
            }

        }
   }
}
