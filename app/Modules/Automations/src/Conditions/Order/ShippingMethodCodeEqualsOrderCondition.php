<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class ShippingMethodCodeEqualsOrderCondition extends BaseOrderConditionAbstract
{
    /**
     * @param $condition_value
     * @return bool
     */
    public function isValid($condition_value): bool
    {
        $result = $this->event->order->shipping_method_code === $condition_value;

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
