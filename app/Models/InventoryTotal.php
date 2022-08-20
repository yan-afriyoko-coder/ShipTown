<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *  InventoryTotal model
 *  @property int $id
 *  @property int $product_id
 *  @property int $quantity
 *  @property int $quantity_reserved
 *  @property int $quantity_incoming
 *  @property int $quantity_available
 *
 *  @property Product $product
 */
class InventoryTotal extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'quantity_reserved',
        'quantity_incoming',
    ];

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
