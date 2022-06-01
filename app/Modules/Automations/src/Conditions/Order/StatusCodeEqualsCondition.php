<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class StatusCodeEqualsCondition extends BaseOrderConditionAbstract
{
    /**
     * @param Builder $query
     * @param $expected_value
     * @return Builder
     */
    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        return $query->where('status_code', '=', $expected_value);
    }

    /**
     * @param $expected_value
     * @return bool
     */
    public function isValid($expected_value): bool
    {
        $result = $this->event->order->status_code === $expected_value;

        Log::debug('Automation condition', [
            'order_number' => $this->event->order->order_number,
            'result' => $result,
            'class' => class_basename(self::class),
            'expected_status' => $expected_value,
            'actual_status' => $this->event->order->status_code,
        ]);

        return $result;
    }
}
