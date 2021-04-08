<?php

namespace App\Models;

use App\User;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * App\Models\OrderShipment
 *
 * @property int $id
 * @property int $order_id
 * @property string $shipping_number
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Order $order
 * @property-read User|null $user
 * @method static Builder|OrderShipment newModelQuery()
 * @method static Builder|OrderShipment newQuery()
 * @method static Builder|OrderShipment query()
 * @method static Builder|OrderShipment whereCreatedAt($value)
 * @method static Builder|OrderShipment whereId($value)
 * @method static Builder|OrderShipment whereOrderId($value)
 * @method static Builder|OrderShipment whereShippingNumber($value)
 * @method static Builder|OrderShipment whereUpdatedAt($value)
 * @method static Builder|OrderShipment whereUserId($value)
 * @mixin Eloquent
 * @property-read mixed $age_in_days
 * @method static Builder|OrderShipment whereAgeInDaysBetween($ageInDaysFrom, $ageInDaysTo)
 */
class OrderShipment extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'order_id',
        'user_id',
        'shipping_number',
    ];

    protected $appends = [
        'age_in_days'
    ];

    /**
     * @return BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAgeInDaysAttribute()
    {
        return Carbon::now()->ceilDay()->diffInDays($this->created_at);
    }

    public function scopeWhereAgeInDaysBetween($query, $ageInDaysFrom, $ageInDaysTo)
    {
        return $query->whereRaw("DATEDIFF(now(), `". $this->getConnection()->getTablePrefix() . $this->getTable()."`.`created_at`) BETWEEN $ageInDaysFrom AND $ageInDaysTo");
    }

    /**
     * @return QueryBuilder
     */
    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(OrderShipment::class)
            ->allowedFilters([
                AllowedFilter::partial('shipping_number'),
                AllowedFilter::exact('order.status_code'),
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('order_id'),

                AllowedFilter::scope('age_in_days_between', 'whereAgeInDaysBetween'),
                'created_at',
                'updated_at',
            ])
            ->allowedIncludes([
                'order',
                'user'
            ])
            ->defaultSort('-id')
            ->allowedSorts([
                'id'
            ]);
    }
}
