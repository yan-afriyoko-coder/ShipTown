<?php

namespace App\Models;

use App\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
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
 */
class OrderShipment extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'order_id',
        'shipping_number',
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

    /**
     * @return QueryBuilder
     */
    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(OrderShipment::class)
            ->allowedFilters([
            ])
            ->allowedIncludes([
            ])
            ->allowedSorts([
            ]);
    }
}
