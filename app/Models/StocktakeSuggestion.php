<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
