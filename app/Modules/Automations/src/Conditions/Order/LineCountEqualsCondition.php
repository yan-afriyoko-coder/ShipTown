<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class LineCountEqualsCondition extends BaseOrderConditionAbstract
{
    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        return $query->whereHas('orderProductsTotals', function ($query) use ($expected_value) {
            $query->where('count', '=', $expected_value);
        });
    }

    /**
     * @param string $condition_value
     * @return bool
     */
    public function isValid(string $condition_value): bool
    {
        $numericValue = intval($condition_value);

        $result = is_numeric($condition_value)
            && $this->event->order->orderTotals->product_line_count === $numericValue;

        Log::debug('Automation condition', [
            'order_number' => $this->event->order->order_number,
            'result' => $result,
            'class' => class_basename(self::class),
            'expected' => $numericValue,
            'actual' => $this->event->order->orderTotals->product_line_count,
        ]);

        return $result;
    }
}
