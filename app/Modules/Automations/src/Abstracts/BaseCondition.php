<?php

namespace App\Modules\Automations\src\Abstracts;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
abstract class BaseCondition
{
    /**
     * @var ActiveOrderCheckEvent|OrderCreatedEvent|OrderUpdatedEvent
     */
    protected $event;

    /**
     * @param $event
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    public static function ordersQueryScope(Builder $query, $expected_value): Builder
    {
        return $query;
    }


    /**
     * @param string $expected_value
     * @return bool
     */
    public function isValid(string $expected_value): bool
    {
        return false;
    }
}
