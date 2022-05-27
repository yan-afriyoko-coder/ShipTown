<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Conditions\BaseCondition;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class IsPartiallyPaidCondition extends BaseCondition
{
    /**
     * @param string $condition_value
     * @return bool
     */
    public function isValid(string $condition_value): bool
    {
        $expectedBoolValue = filter_var($condition_value, FILTER_VALIDATE_BOOL);

        $order = $this->event->order;

        $isPartiallyPaid = ($order->total_paid > 0)  && ($order->total_paid < $order->total);

        $result = $isPartiallyPaid === $expectedBoolValue;

        Log::debug('Automation condition', [
            'order_number' => $order->order_number,
            'result' => $result,
            'class' => class_basename(self::class),
            'total_paid' => $order->total_paid,
            'expected' => $expectedBoolValue,
            'actual' => $isPartiallyPaid
        ]);

        return $result;
    }
}
