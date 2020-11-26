<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\OrderStatus
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $order_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|OrderStatus newModelQuery()
 * @method static Builder|OrderStatus newQuery()
 * @method static Builder|OrderStatus query()
 * @method static Builder|OrderStatus whereCode($value)
 * @method static Builder|OrderStatus whereCreatedAt($value)
 * @method static Builder|OrderStatus whereId($value)
 * @method static Builder|OrderStatus whereName($value)
 * @method static Builder|OrderStatus whereOrderActive($value)
 * @method static Builder|OrderStatus whereUpdatedAt($value)
 * @mixin Eloquent
 */
class OrderStatus extends Model
{
    public static $activeOrderStatusList = [
        'paid',
        'picking',
        'picking_web',
        'picking_warehouse',
        'packing',
        'packing_web',
        'packing_warehouse',
        'for_later',
        'single_line_orders',
    ];

    public static $toFollowStatusList = [
        'processing',
        'unshipped',
        'partially_shipped',
        'holded',
        'on_hold',
        'missing_item',
        'auto_missing_item',
    ];

    public static $completedStatusCodeList = [
        'cancelled',
        'canceled',
        'closed',
        'complete',
        'completed_imported_to_rms',
        'ready'
    ];

    public static function getActiveStatusCodesList()
    {
        return self::$activeOrderStatusList;
    }

    public static function getToFollowStatusList()
    {
        return self::$toFollowStatusList;
    }

    public static function getCompletedStatusCodeList()
    {
        return self::$completedStatusCodeList;
    }

    public static function getOpenStatuses()
    {
        return array_merge(
            static::getActiveStatusCodesList(),
            static::getToFollowStatusList()
        );
    }

    public static function getClosedStatuses()
    {
        return array_merge(
            static::getCompletedStatusCodeList()
        );
    }

    public static function isActive(string $status_code)
    {
        return array_search($status_code, self::getActiveStatusCodesList()) != false;
    }

    public static function isToFollow(string $status_code)
    {
        return array_search($status_code, self::getToFollowStatusList()) != false;
    }

    public static function isComplete(string $status_code)
    {
        return array_search($status_code, self::getCompletedStatusCodeList()) != false;
    }
}
