<?php

namespace App\Modules\Automations\src\Executors;

use App\Events\Order\OrderCreatedEvent;

class ChangeOrderStatusToExecutor
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
