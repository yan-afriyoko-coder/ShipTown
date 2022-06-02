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
        $expectedStatuses = collect(explode(',', $expected_value))
            ->filter()
            ->transform(function ($record) {
                return trim($record);
            });

        return $query->whereIn('status_code', $expectedStatuses);
    }
}
