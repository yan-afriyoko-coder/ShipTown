<?php

namespace App\Modules\Automations\src\Validators;

use App\Events\Order\OrderCreatedEvent;

/**
 *
 */
class OrderShippingMethodCodeEqualsValidator
{
    private OrderCreatedEvent $event;

    public function __construct(OrderCreatedEvent $event)
    {
        $this->event = $event;
    }

    /**
     * @param $condition_value
     * @return bool
     */
    public function isValid($condition_value): bool
    {
        return $this->event->order->shipping_method_code === $condition_value;
    }
}
