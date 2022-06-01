<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class StatusCodeInCondition extends BaseOrderConditionAbstract
{
    /**
     * @param Builder $query
     * @param $expected_value
     * @return Builder
     */
    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        static::invalidateQueryIf($query, trim($expected_value) === '');

        $expectedStatuses = explode(',', $expected_value);

        return $query->whereIn('status_code', $expectedStatuses);
    }

    /**
     * @param $expected_value
     * @return bool
     */
    public function isValid($expected_value): bool
    {
        $expectedStatuses = explode(',', $expected_value);

        $result = in_array($this->event->order->status_code, $expectedStatuses) === true;

        Log::debug('Automation condition', [
            'order_number' => $this->event->order->order_number,
            'result' => $result,
            'class' => class_basename(self::class),
            'expected_statuses' => $expected_value,
            'actual_status' => $this->event->order->status_code,
        ]);

        return $result;
    }
}
