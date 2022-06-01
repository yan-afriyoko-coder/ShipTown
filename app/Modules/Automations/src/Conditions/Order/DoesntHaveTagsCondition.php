<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
class DoesntHaveTagsCondition extends BaseOrderConditionAbstract
{
    public static function ordersQueryScope(Builder $query, $expected_value): Builder
    {
        if (trim($expected_value) === '') {
            // empty value automatically invalidates query
            return $query->whereRaw('( "has_tags_condition"="" )');
        }

        $tagsArray = explode(',', $expected_value);

        return $query->withoutAllTags($tagsArray);
    }
}
