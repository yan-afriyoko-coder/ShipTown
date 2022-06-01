<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class TotalQuantityToShipEqualsCondition extends BaseOrderConditionAbstract
{
    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        if (! is_numeric($expected_value)) {
            // empty value automatically invalidates query
            return $query->whereRaw('(1=2)');
        }

        return $query->whereHas('orderProductsTotals', function ($query) use ($expected_value) {
            $query->where('quantity_to_ship', '=', $expected_value);
        });
    }
}
