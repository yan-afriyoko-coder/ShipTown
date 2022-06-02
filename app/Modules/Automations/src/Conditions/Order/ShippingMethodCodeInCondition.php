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
        $shippingMethods = collect(explode(',', $expected_value))
            ->filter()
            ->transform(function ($record) {
                return trim($record);
            });

        return $query->whereIn('shipping_method_code', $shippingMethods);
    }
}
