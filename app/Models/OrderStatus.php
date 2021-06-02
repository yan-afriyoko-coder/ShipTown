<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\OrderStatus
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property bool $reserves_stock
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
 * @method static Builder|OrderStatus whereReservesStock(bool $reserves_stock)
 */
class OrderStatus extends Model
{
    protected $fillable = [
        'name',
        'code',
        'status_code',
        'is_open',
        'reserves_stock',
    ];

    protected $attributes = [
        'reserves_stock' => true,
    ];

    public static $toFollowStatusList = [
        'processing',
        'unshipped',
        'partially_shipped',
        'holded',
        'on_hold',
        'missing_item',
        'auto_missing_item',
        'ready'
    ];

    public static $completedStatusCodeList = [
        'cancelled',
        'canceled',
        'closed',
        'complete',
        'completed_imported_to_rms',
    ];

    public static function getToFollowStatusList()
    {
        return self::$toFollowStatusList;
    }

    public static function getCompletedStatusCodeList()
    {
        return self::$completedStatusCodeList;
    }

    public static function getClosedStatuses()
    {
        return array_merge(
            static::getCompletedStatusCodeList()
        );
    }

    public static function getStatusesReservingStock()
    {
        return array_merge(
            static::getCompletedStatusCodeList()
        );
    }

    /**
     * @param string $status_code
     * @return bool
     */
    public static function isActive(string $status_code): bool
    {
        if (self::isComplete($status_code) || self::isToFollow($status_code)) {
            return false;
        }

        return true;
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
