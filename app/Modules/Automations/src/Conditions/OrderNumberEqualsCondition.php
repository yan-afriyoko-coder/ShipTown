<?php

namespace App\Modules\Automations\src\Conditions;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class OrderNumberEqualsCondition extends BaseOrderConditionAbstract
{
    /**
     * @var ActiveOrderCheckEvent|OrderCreatedEvent|OrderUpdatedEvent
     */
    protected $event;

    public static function ordersQueryScope(Builder $query, $expected_status_code): Builder
    {
        $query->where(['order_number' => $expected_status_code]);

        return $query;
    }

    /**
     * @param string $expected_value
     * @return bool
     */
    public function isValid(string $expected_value): bool
    {
        $actual_value = $this->event->order->order_number;

        $result = $actual_value === $expected_value;

        Log::debug('Automation condition', [
            'order_number' => $this->event->order->order_number,
            'result' => $result,
            'class' => class_basename(self::class),
            'expected_value' => $expected_value,
            '$actual_value' => $actual_value,
        ]);

        return $result;
    }
}
