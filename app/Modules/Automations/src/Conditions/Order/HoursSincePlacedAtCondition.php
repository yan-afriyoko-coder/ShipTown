<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
class HoursSincePlacedAtCondition extends BaseOrderConditionAbstract
{
    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        $trimmedValue = trim($expected_value);

        static::invalidateQueryIf($query, $trimmedValue === '');

        static::invalidateQueryIf($query, is_int($trimmedValue) === false);

        return $query->whereRaw('TIMESTAMPDIFF(hour, order_placed_at, now()) > ?', [(int)$trimmedValue]);
    }
}
