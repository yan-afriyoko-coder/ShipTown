<?php

namespace App\Models;

use App\BaseModel;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * App\Models\OrderStatus.
 *
 * @property int         $id
 * @property string      $code
 * @property string      $name
 * @property bool        $reserves_stock
 * @property bool         $order_active
 * @property bool         $sync_ecommerce
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
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
 * @mixin Eloquent
 */
class OrderStatus extends BaseModel
{
    use SoftDeletes;

    protected $table = 'orders_statuses';

    protected $fillable = [
        'name',
        'code',
        'order_active',
        'hidden',
        'reserves_stock',
        'sync_ecommerce',
    ];

    protected $casts = [
        'order_active' => 'boolean',
        'reserves_stock' => 'boolean',
        'sync_ecommerce' => 'boolean',
    ];

    protected $attributes = [
        'order_active' => true,
        'reserves_stock' => true,
        'sync_ecommerce' => false,
    ];

    public static $toFollowStatusList = [
        'processing',
        'unshipped',
        'partially_shipped',
        'holded',
        'on_hold',
        'missing_item',
        'auto_missing_item',
        'ready',
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
     *
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

    /**
     * @return QueryBuilder
     */
    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(OrderStatus::class)
            ->allowedFilters([
                AllowedFilter::exact('hidden')
            ])
            ->allowedIncludes([])
            ->allowedSorts([
                'updated_at',
                'code',
            ]);
    }
}
