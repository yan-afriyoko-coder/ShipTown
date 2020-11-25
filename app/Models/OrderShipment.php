<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class OrderShipment
 * @package App\Models
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
