<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use Log;

/**
 *
 */
class TotalQuantityToShipEqualsCondition
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
        if (! is_numeric($condition_value)) {
            $result = false;
        } else {
            $numericValue = floatval($condition_value);

            $totalQuantityToShip = $this->event->order->orderProducts()->sum('quantity_to_ship');

            $result = $totalQuantityToShip === $numericValue;
        }

        Log::debug('Automation condition', [
            'order_number' => $this->event->order->order_number,
            'result' => $result,
            'class' => class_basename(self::class),
            'expected' => $numericValue ?? '',
            'actual' => $totalQuantityToShip ?? '',
        ]);

        return $result;
    }
}
