<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PickRequest
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
}
