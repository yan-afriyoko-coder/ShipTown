<?php

namespace App\Modules\Automations\src\Conditions;

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
        if (trim($expected_value) === '') {
            // empty value automatically invalidates query
            return $query->whereRaw('(1=2)');
        }

        $shippingMethods = explode(',', $expected_value);

        return $query->whereIn('shipping_method_code', $shippingMethods);
    }
}
