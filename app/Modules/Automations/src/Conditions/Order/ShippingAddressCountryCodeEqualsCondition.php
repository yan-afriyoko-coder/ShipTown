<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;

class ShippingAddressCountryCodeEqualsCondition extends BaseOrderConditionAbstract
{
    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        return $query->whereHas('shippingAddress', function (Builder $query) use ($expected_value) {
            $query->where('country_code', '=', $expected_value);
        });
    }
}
