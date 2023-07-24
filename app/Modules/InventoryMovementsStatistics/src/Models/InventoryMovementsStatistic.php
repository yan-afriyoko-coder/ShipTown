<?php

namespace App\Modules\InventoryMovementsStatistics\src\Models;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
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

    protected $casts = [
        'quantity_sold_last_7_days' => 'double',
        'quantity_sold_last_14_days' => 'double',
        'quantity_sold_last_28_days' => 'double',
        'quantity_sold_this_week' => 'double',
        'quantity_sold_last_week' => 'double',
        'quantity_sold_2weeks_ago' => 'double',
        'quantity_sold_3weeks_ago' => 'double',
        'quantity_sold_4weeks_ago' => 'double',
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
