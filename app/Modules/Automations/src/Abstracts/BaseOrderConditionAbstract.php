<?php

namespace App\Modules\Automations\src\Abstracts;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
abstract class BaseOrderConditionAbstract
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

    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        return $query;
    }

    protected static function invalidateQueryIf($query, bool $shouldInvalidate): void
    {
        if ($shouldInvalidate) {
            $query->whereRaw('(1=2)');
        }
    }

    protected static function invalidateQueryUnless($query, bool $shouldInvalidate): void
    {
        static::invalidateQueryIf($query, ! $shouldInvalidate);
    }

    /**
     * @param string $expected_value
     * @return bool
     */
    public function isValid(string $expected_value): bool
    {
        return true;
    }
}
