<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class IsFullyPackedCondition
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
        $expectedBoolValue = filter_var($condition_value, FILTER_VALIDATE_BOOL);

        $hasAnythingToShip = $this->event->order->orderProducts()->where('quantity_to_ship', '>', 0)->exists();

        $isPacked = ! $hasAnythingToShip;

        $result = $isPacked === $expectedBoolValue;

        Log::debug('Automation condition', [
            'order_number' => $this->event->order->order_number,
            'class' => class_basename(self::class),
            'is_packed' => $isPacked,
            'expected' => $expectedBoolValue,
            'actual' => $result,
        ]);

        return $result;
    }
}
