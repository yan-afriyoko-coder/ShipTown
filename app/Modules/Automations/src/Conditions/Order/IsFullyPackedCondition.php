<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Conditions\BaseCondition;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class IsFullyPackedCondition extends BaseCondition
{
    /**
     * @param string $condition_value
     * @return bool
     */
    public function isValid(string $condition_value): bool
    {
        $expectedBoolValue = filter_var($condition_value, FILTER_VALIDATE_BOOL);

        $hasAnythingToShip = $this->event->order->orderProducts()->where('quantity_to_ship', '>', 0)->exists();

        $isPacked = !$hasAnythingToShip;

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
