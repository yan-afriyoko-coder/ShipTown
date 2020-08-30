<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use phpseclib\Math\BigInteger;

/**
 * Class PickRequest
 * @property BigInteger pick_id
 * @property float quantity_required
 * @package App\Models
 */
class PickRequest extends Model
{
    protected $fillable = [
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
