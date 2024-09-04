<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;

class StatusCodeInCondition extends BaseOrderConditionAbstract
{
    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        static::invalidateQueryIf($query, trim($expected_value) === '');

        $statusCodes = explode(',', $expected_value);

        return $query->whereIn('status_code', $statusCodes);
    }
}
