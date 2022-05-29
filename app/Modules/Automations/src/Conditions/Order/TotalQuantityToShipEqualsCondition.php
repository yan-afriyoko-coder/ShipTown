<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class TotalQuantityToShipEqualsCondition extends BaseOrderConditionAbstract
{
    /**
     * @param string $expected_value
     * @return bool
     */
    public function isValid(string $expected_value): bool
    {
        if (! is_numeric($expected_value)) {
            $result = false;
        } else {
            $conditionFloatValue = floatval($expected_value);

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
