<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class IsFullyPaidCondition extends BaseOrderConditionAbstract
{
    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        if ($expected_value === '') {
            $expected_value = 'true';
        }

        $expectedBoolValue = filter_var($expected_value, FILTER_VALIDATE_BOOL);

        return $query->whereHas('orderProductsTotals', function (Builder $query) use ($expectedBoolValue) {
            $query->whereRaw('(
                    ((round(total_products, 2) > 0) OR (round(total_paid, 2) > 0) OR (round(total_discounts, 2) > 0))
                    AND ((round(total_paid, 2) + round(total_discounts, 2)) >= total_products + total_shipping)
                ) = ?
                ', [$expectedBoolValue]);
        });
    }
}
