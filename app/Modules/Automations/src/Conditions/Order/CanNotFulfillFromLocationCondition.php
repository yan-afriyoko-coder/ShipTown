<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Events\Order\OrderCreatedEvent;
use App\Services\OrderService;
use Log;

/**
 *
 */
class CanNotFulfillFromLocationCondition
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
        $result = OrderService::canNotFulfill($this->event->order, $condition_value);

        Log::debug('Validating condition', [
            'order_number' => $this->event->order->order_number,
            'location_id' => $condition_value,
            'can_NOT_fulfill' => $result,
            'class' => self::class,
        ]);

        return $result;
    }
}
