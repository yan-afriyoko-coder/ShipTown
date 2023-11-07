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
                    ((total_products > 0) OR (round(total_paid, 2) > 0))
                    AND ((round(total_paid, 2) > 0) OR (total_discounts > 0))
                    AND ((round(total_paid, 2) + total_discounts) >= total_price + total_shipping)
                ) = ?
                ', [$expectedBoolValue]);
        });
    }
}
