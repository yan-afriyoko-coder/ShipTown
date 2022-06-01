<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
class ShippingMethodCodeInCondition extends BaseOrderConditionAbstract
{
    /**
     * @param Builder $query
     * @param $expected_value
     * @return Builder
     */
    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        static::invalidateQueryIf($query, trim($expected_value) === '');

        $shippingMethods = explode(',', $expected_value);

        return $query->whereIn('shipping_method_code', $shippingMethods);
    }
}
