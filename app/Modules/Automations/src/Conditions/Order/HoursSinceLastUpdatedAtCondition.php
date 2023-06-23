<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
class HoursSinceLastUpdatedAtCondition extends BaseOrderConditionAbstract
{
    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        $trimmedValue = trim($expected_value);

        static::invalidateQueryIf($query, $trimmedValue === '');

        if (is_int($trimmedValue) === true) {
            static::invalidateQueryIf($query, true);
            return $query;
        }

        $maxOrderPlacedAtDate = now()->subHours((int)$trimmedValue)->toDateTimeString();

        return $query->whereDate('updated_at', '<=', $maxOrderPlacedAtDate);
    }
}
