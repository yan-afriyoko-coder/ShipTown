<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
class IsPartiallyPaidCondition extends BaseOrderConditionAbstract
{
    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        return $query->whereRaw('((total_paid > 0) AND (total_paid < total))');
    }
}
