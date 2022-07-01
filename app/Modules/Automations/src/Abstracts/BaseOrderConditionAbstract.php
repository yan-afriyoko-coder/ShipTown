<?php

namespace App\Modules\Automations\src\Abstracts;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
abstract class BaseOrderConditionAbstract
{
    /**
     * @var Order
     */
    protected Order $order;

    /**
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        return $query;
    }

    protected static function invalidateQueryIf($query, bool $shouldInvalidate): void
    {
        if ($shouldInvalidate) {
            $query->whereRaw('0 = 1');
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
    public function isValid(string $expected_value = ''): bool
    {
        return true;
    }
}
