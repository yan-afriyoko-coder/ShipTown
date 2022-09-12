<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 *
 * @property int inventory_id
 * @property int points
 * @property string reason
 */
class StocktakeSuggestion extends Model
{
    protected $table = 'stocktake_suggestions';

    protected $fillable = [
        'inventory_id',
        'points',
        'reason',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function product(): HasOneThrough
    {
        return $this->hasOneThrough(Product::class, Inventory::class, 'id', 'id', 'inventory_id', 'product_id');
    }

    public function warehouse(): HasOneThrough
    {
        return $this->hasOneThrough(Warehouse::class, Inventory::class, 'id', 'id', 'inventory_id', 'warehouse_id');
    }
}
