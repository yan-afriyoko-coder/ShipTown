<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Services\OrderService;
use Log;

/**
 *
 */
class IsFullyPaidCondition
{
    private OrderUpdatedEvent $event;

    public function __construct(OrderUpdatedEvent $event)
    {
        $this->event = $event;
    }

    /**
     * @param $condition_value
     * @return bool
     */
    public function isValid($condition_value): bool
    {
        $result = $this->event->order->isPaid === $condition_value;

        Log::debug('Validating condition', [
            'order_number' => $this->event->order->order_number,
            'isPaid' => $this->event->order->isPaid,
            'class' => self::class,
        ]);

        return $result;
    }
}
