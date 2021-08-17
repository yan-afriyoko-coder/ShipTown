<?php

namespace App\Modules\Automations\src\Executors\Order;

use App\Events\Order\OrderCreatedEvent;

class SetStatusCodeExecutor
{
    private OrderCreatedEvent $event;

    public function __construct(OrderCreatedEvent $event)
    {
        $this->event = $event;
    }

    /**
     * @param $value
     */
    public function handle($value)
    {
        $order = $this->event->order;

        $order->update(['status_code' => $value]);
    }
}
