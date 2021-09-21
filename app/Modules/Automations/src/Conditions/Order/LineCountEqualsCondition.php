<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use Log;

/**
 *
 */
class LineCountEqualsCondition
{
    /**
     * @var ActiveOrderCheckEvent|OrderCreatedEvent|OrderUpdatedEvent
     */
    private $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * @param string $condition_value
     * @return bool
     */
    public function isValid(string $condition_value): bool
    {
        if (!is_numeric($condition_value)) {
            Log::warning('Incorrect condition value, number expected', [
                'order_number' => $this->event->order->order_number,
                'value' => $condition_value,
                'class' => self::class,
            ]);

            return false;
        }

        $numericValue = intval($condition_value);

        $result = $this->event->order->product_line_count === $numericValue;

        Log::debug('Line Count Equals', [
            'order_number' => $this->event->order->order_number,
            'expected' => $numericValue,
            'actual' => $this->event->order->product_line_count,
            'class' => self::class,
        ]);

        return $result;
    }
}
