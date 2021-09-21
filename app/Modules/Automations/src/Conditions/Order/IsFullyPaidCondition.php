<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Events\Order\ActiveOrderCheckEvent;
use Log;

/**
 *
 */
class IsFullyPaidCondition
{
    private ActiveOrderCheckEvent $event;

    public function __construct(ActiveOrderCheckEvent $event)
    {
        $this->event = $event;
    }

    /**
     * @param bool $condition_value
     * @return bool
     */
    public function isValid(bool $condition_value): bool
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
