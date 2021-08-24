<?php

namespace App\Models;

use App\BaseModel;
use App\Traits\LogsActivityTrait;
use App\User;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;

/**
 * App\Models\OrderProduct.
 *
 * @property int         $id
 * @property int|null    $order_id
 * @property int|null    $product_id
 * @property float       $quantity_shipped
 * @property float       $quantity_to_ship
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin Eloquent
 */
class OrderProductShipment extends BaseModel
{
    protected $table = 'orders_products_shipments';

    protected $fillable = [
        'user_id',
        'order_product_id',
        'quantity_shipped',
        'order_shipment_id',
    ];

    protected $casts = [
        'quantity_shipped' => 'float',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
