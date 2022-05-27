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
 * @property bool        $order_active
 * @property bool        $order_on_hold
 * @property bool        $sync_ecommerce
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
        'order_on_hold',
        'hidden',
        'sync_ecommerce',
    ];

    protected $casts = [
        'order_active'      => 'boolean',
        'order_on_hold'     => 'boolean',
        'hidden'            => 'boolean',
        'sync_ecommerce'    => 'boolean',
    ];

    protected $attributes = [
        'order_active'   => true,
        'order_on_hold'  => false,
        'hidden'         => false,
        'sync_ecommerce' => false,
    ];

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
