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
     * @param $expected_value
     * @return bool
     */
    public function isValid($expected_value): bool
    {
        $expected = explode(',', $expected_value);

        $result = in_array($this->event->order->shipping_method_code, $expected) === false;

        Log::debug('Automation condition', [
            'order_number' => $this->event->order->order_number,
            'result' => $result,
            'class' => class_basename(self::class),
            'expected_shipping_method_code' => $expected_value,
            'actual_shipping_method_code' => $this->event->order->shipping_method_code,
        ]);

        return $result;
    }
}
