<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class StatusCodeNotInOrderConditionAbstract extends BaseOrderConditionAbstract
{
    /**
     * @param $condition_value
     * @return bool
     */
    public function isValid($condition_value): bool
    {
        $expectedStatuses = explode(',', $condition_value);

        $result = in_array($this->event->order->status_code, $expectedStatuses) === false;

        Log::debug('Automation condition', [
            'order_number' => $this->event->order->order_number,
            'result' => $result,
            'class' => class_basename(self::class),
            'expected_statuses' => $condition_value,
            'actual_status' => $this->event->order->status_code,
        ]);

        return $result;
    }
}
