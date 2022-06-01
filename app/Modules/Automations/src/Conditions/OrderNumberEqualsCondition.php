<?php

namespace App\Modules\Automations\src\Conditions;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
class OrderNumberEqualsCondition extends BaseOrderConditionAbstract
{
    /**
     * @var ActiveOrderCheckEvent|OrderCreatedEvent|OrderUpdatedEvent
     */
    protected $event;

    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        $query->where(['order_number' => $expected_value]);

        return $query;
    }
}
