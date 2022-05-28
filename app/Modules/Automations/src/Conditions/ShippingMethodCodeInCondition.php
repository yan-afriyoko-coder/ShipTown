<?php

namespace App\Modules\Automations\src\Conditions;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class ShippingMethodCodeInCondition extends BaseOrderConditionAbstract
{
    /**
     * @param $condition_value
     * @return bool
     */
    public function isValid($condition_value): bool
    {
        $expected = explode(',', $condition_value);

        $result = in_array($this->event->order->status_code, $expected) === false;

        Log::debug('Automation condition', [
            'order_number' => $this->event->order->order_number,
            'result' => $result,
            'class' => class_basename(self::class),
            'expected_shipping_method_code' => $condition_value,
            'actual_shipping_method_code' => $this->event->order->shipping_method_code,
        ]);

        return $result;
    }
}
