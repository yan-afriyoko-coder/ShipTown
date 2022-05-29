<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class IsFullyPaidCondition extends BaseOrderConditionAbstract
{
    public static function ordersQueryScope(Builder $query, $expected_value): Builder
    {
        $expectedBoolValue = filter_var($expected_value, FILTER_VALIDATE_BOOL);

        return $query->where(DB::raw('(total_paid >= total)'), '=', $expectedBoolValue);
    }

    /**
     * @param string $condition_value
     * @return bool
     */
    public function isValid(string $condition_value): bool
    {
        $expectedBoolValue = filter_var($condition_value, FILTER_VALIDATE_BOOL);

        $result = $this->event->order->isPaid === $expectedBoolValue;

        Log::debug('Automation condition', [
            'order_number' => $this->event->order->order_number,
            'result' => $result,
            'class' => class_basename(self::class),
            'expected_isPaid' => $expectedBoolValue,
            'actual_isPaid' => $this->event->order->isPaid,
        ]);

        return $result;
    }
}
