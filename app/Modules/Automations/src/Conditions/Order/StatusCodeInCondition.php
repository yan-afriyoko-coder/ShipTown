<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
class StatusCodeInCondition extends BaseOrderConditionAbstract
{
    /**
     * @param Builder $query
     * @param $expected_value
     * @return Builder
     */
    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        static::invalidateQueryIf($query, trim($expected_value) === '');

        $expectedStatuses = explode(',', $expected_value);

        return $query->whereIn('status_code', $expectedStatuses);
    }
}
