<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Conditions\BaseCondition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class StatusCodeEqualsCondition extends BaseCondition
{

    /**
     * @param Builder $query
     * @param $expected_status_code
     * @return Builder
     */
    public static function ordersQueryScope(Builder $query, $expected_status_code): Builder
    {
        return $query->where('status_code', '=', $expected_status_code);
    }

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
