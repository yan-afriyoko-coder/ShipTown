<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Events\Order\OrderCreatedEvent;
use Log;

/**
 *
 */
class LineCountEqualsCondition
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
        $result = $this->event->order->order_products_count === $condition_value;

        Log::debug('Line Count Equals', [
            'order_number' => $this->event->order->order_number,
            'expected' => $condition_value,
            'actual' => $this->event->order->order_products_count,
            'class' => self::class,
        ]);

        return $result;
    }
}
