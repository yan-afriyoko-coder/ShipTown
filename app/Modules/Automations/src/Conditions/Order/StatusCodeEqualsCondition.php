<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Conditions\BaseCondition;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class StatusCodeEqualsCondition extends BaseCondition
{
    /**
     * @param $condition_value
     * @return bool
     */
    public function isValid($condition_value): bool
    {
        $result = $this->event->order->status_code === $condition_value;

        Log::debug('Automation condition', [
            'order_number' => $this->event->order->order_number,
            'result' => $result,
            'class' => class_basename(self::class),
            'expected_status' => $condition_value,
            'actual_status' => $this->event->order->status_code,
        ]);

        return $result;
    }
}
