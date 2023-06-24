<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 * @property int $id
 * @property int $description
 * @property int $product_id
 * @property int $inventory_id
 * @property int $warehouse_id
 * @property string $warehouse_code
 * @property double $quantity_sold
 */
class InventoryMovementsStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'inventory_id',
        'product_id',
        'warehouse_id',
        'warehouse_code',
        'quantity_sold',
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
