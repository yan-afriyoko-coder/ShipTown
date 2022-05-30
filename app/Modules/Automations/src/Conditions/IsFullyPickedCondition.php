<?php

namespace App\Modules\Automations\src\Conditions;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class IsFullyPickedCondition extends BaseOrderConditionAbstract
{
    public static function ordersQueryScope(Builder $query, $expected_value): Builder
    {
        if ($expected_value === '') {
            $expected_value = 'true';
        }

        $expected_value = filter_var($expected_value, FILTER_VALIDATE_BOOL);

        return $query->whereHas('orderProductsTotals', function ($query) use ($expected_value) {
            $query->where(DB::raw('(orders_products_totals.quantity_to_pick = 0)'), '=', $expected_value);
        });
    }

    /**
     * @param string $condition_value
     * @return bool
     */
    public function isValid(string $condition_value): bool
    {
        $expectedBoolValue = filter_var($condition_value, FILTER_VALIDATE_BOOL);

        $result = $this->event->order->is_picked === $expectedBoolValue;

        Log::debug('Automation condition', [
            'order_number' => $this->event->order->order_number,
            'class' => class_basename(self::class),
            'is_picked' => $this->event->order->is_packed,
            'expected' => $expectedBoolValue,
            'actual' => $result,
        ]);

        return $result;
    }
}
