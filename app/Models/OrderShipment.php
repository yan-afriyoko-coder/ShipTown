<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
