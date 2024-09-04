<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;

class StatusCodeNotInCondition extends BaseOrderConditionAbstract
{
    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        static::invalidateQueryIf($query, trim($expected_value) === '');

        $expectedStatuses = explode(',', $expected_value);

        return $query->whereNotIn('status_code', $expectedStatuses);
    }
}
