<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Events\Order\OrderCreatedEvent;
use Log;

/**
 *
 */
class StatusCodeEqualsCondition
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
        $result = $this->event->order->status_code === $condition_value;

        Log::debug('Validating condition', [
            'class' => self::class,
            'order_number' => $this->event->order->order_number,
            'expected_status' => $condition_value,
            'actual_status' => $this->event->order->status_code,
        ]);

        return $result;
    }
}
