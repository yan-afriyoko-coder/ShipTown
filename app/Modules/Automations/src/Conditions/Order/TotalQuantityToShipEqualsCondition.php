<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use Illuminate\Support\Facades\Log;

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
            $conditionFloatValue = floatval($condition_value);

            $totalQuantityToShip = floatval($this->event->order->orderProducts()->sum('quantity_to_ship'));

            $result = $totalQuantityToShip === $conditionFloatValue;
        }

        Log::debug('Automation condition', [
            'order_number' => $this->event->order->order_number,
            'result' => $result,
            'class' => class_basename(self::class),
            'expected' => $conditionFloatValue ?? '',
            'actual' => $totalQuantityToShip ?? '',
        ]);

        return $result;
    }
}
