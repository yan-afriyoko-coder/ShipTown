<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Inventory
 *
 * @property int $id
 * @property int|null $warehouse_id
 * @property int $product_id
 * @property int $location_id
 * @property string $shelve_location
 * @property string $quantity
 * @property string $quantity_reserved
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $quantity_available
 * @property-read Product $product
 * @method static Builder|Inventory newModelQuery()
 * @method static Builder|Inventory newQuery()
 * @method static Builder|Inventory query()
 * @method static Builder|Inventory whereCreatedAt($value)
 * @method static Builder|Inventory whereDeletedAt($value)
 * @method static Builder|Inventory whereId($value)
 * @method static Builder|Inventory whereLocationId($value)
 * @method static Builder|Inventory whereProductId($value)
 * @method static Builder|Inventory whereQuantity($value)
 * @method static Builder|Inventory whereQuantityReserved($value)
 * @method static Builder|Inventory whereShelveLocation($value)
 * @method static Builder|Inventory whereUpdatedAt($value)
 * @method static Builder|Inventory whereWarehouseId($value)
 * @mixin Eloquent
 */
class Inventory extends Model
{
    protected $fillable = [
        'warehouse_id',
        'location_id',
        'shelve_location',
        'product_id',
        'quantity',
        'quantity_reserved'
    ];

    protected $table = 'inventory';

    protected $appends = [
        "quantity_available"
    ];

    protected $attributes = [
        'quantity' => 0,
        'quantity_reserved' => 0,
    ];

    public function getQuantityAvailableAttribute()
    {
        $quantity_available = $this->quantity - $this->quantity_reserved;

        if ($quantity_available<0) {
            return 0;
        }

        return $quantity_available;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
