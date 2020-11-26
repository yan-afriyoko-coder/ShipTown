<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use phpseclib\Math\BigInteger;

/**
 * App\Models\PickRequest
 *
 * @property int $id
 * @property int|null $order_id
 * @property int $order_product_id
 * @property string $quantity_required
 * @property string $quantity_picked
 * @property int|null $pick_id
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\OrderProduct $orderProduct
 * @property-read \App\Models\Pick|null $pick
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest newQuery()
 * @method static \Illuminate\Database\Query\Builder|PickRequest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest whereOrderProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest wherePickId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest whereQuantityPicked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest whereQuantityRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PickRequest withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PickRequest withoutTrashed()
 * @mixin \Eloquent
 */
class PickRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'order_product_id',
        'quantity_required',
        'quantity_picked',
        'pick_id'
    ];

    /**
     * @return BelongsTo
     */
    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class);
    }

    /**
     * @return BelongsTo
     */
    public function pick()
    {
        return $this->belongsTo(Pick::class);
    }

    public function extractToNewPick($quantity)
    {
        $newPickRequest = $this->replicate();
        $newPickRequest->quantity_required = $quantity;
        $newPickRequest->save();

        $this->decrement('quantity_required', $quantity);

        return $newPickRequest;
    }
}
