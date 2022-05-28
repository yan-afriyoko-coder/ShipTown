<?php

namespace App\Modules\Automations\src\Conditions;

use App\Modules\Automations\src\Abstracts\BaseCondition;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class IsFullyPickedCondition extends BaseCondition
{
    /**
     * @param string $condition_value
     * @return bool
     */
    public function isValid(string $condition_value): bool
    {
        $expectedBoolValue = filter_var($condition_value, FILTER_VALIDATE_BOOL);

        $result = $this->event->order->is_picked === $expectedBoolValue;

        Log::debug('Automation condition', [
            'order_number' => $this->event->order->order_number,
            'class' => class_basename(self::class),
            'is_picked' => $this->event->order->is_packed,
            'expected' => $expectedBoolValue,
            'actual' => $result,
        ]);

        return $result;
    }
}
