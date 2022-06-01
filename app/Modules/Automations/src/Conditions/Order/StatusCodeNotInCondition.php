<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
class StatusCodeNotInCondition extends BaseOrderConditionAbstract
{
    /**
     * @param Builder $query
     * @param $expected_value
     * @return Builder
     */
    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        if (trim($expected_value) === '') {
            // empty value automatically invalidates query
            return $query->whereRaw('( "has_tags_condition"="" )');
        }

        $expectedStatuses = explode(',', $expected_value);

        return $query->whereNotIn('status_code', $expectedStatuses);
    }
}
