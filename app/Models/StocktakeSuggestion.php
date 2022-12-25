<?php

namespace App\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Makeable\LaravelFactory\HasFactory;

/**
 *
 * @property int inventory_id
 * @property int points
 * @property string reason
 * @property int $product_id
 */
class StocktakeSuggestion extends BaseModel
{
    use HasFactory;

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
