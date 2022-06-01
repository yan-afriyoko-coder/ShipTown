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
}
