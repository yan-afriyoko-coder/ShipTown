<?php

namespace App\Models;

use App\BaseModel;
use App\Traits\LogsActivityTrait;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;

/**
 * App\Models\Inventory.
 *
 * @property int         $id
 * @property int|null    $warehouse_id
 * @property int         $product_id
 * @property int         $location_id
 * @property string      $shelve_location
 * @property float       $quantity
 * @property float       $quantity_reserved
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read float $quantity_available
 * @property-read Product $product
 *
 * @property-read Warehouse $warehouse
 *
 * @method static Builder|Inventory increment($column, $quantity)
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
 * @method static Inventory firstOrNew(array $array)
 *
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @mixin Eloquent
 */
class Inventory extends BaseModel
{
    use LogsActivityTrait;

    protected static $logAttributes = [
        'quantity',
        'quantity_reserved',
        'shelve_location',
    ];

    protected $fillable = [
        'warehouse_id',
        'location_id',
        'warehouse_code',
        'shelve_location',
        'product_id',
        'quantity',
        'quantity_reserved',
    ];

    protected $table = 'inventory';

    protected $attributes = [
        'quantity'          => 0,
        'quantity_reserved' => 0,
    ];

    protected $casts = [
        'quantity'           => 'float',
        'quantity_reserved'  => 'float',
        'quantity_available' => 'float',
    ];

    /**
     * @param $query
     * @param $tags
     * @return mixed
     */
    public function scopeWithWarehouseTags($query, $tags)
    {
        return $query->whereHas('warehouse', function (Builder $query) use ($tags) {
            $query->withAllTags($tags);
        });
    }

    public function save(array $options = [])
    {
        if ($this->warehouse_id === null) {
            if ($this->location_id) {
                $warehouse = Warehouse::whereCode($this->location_id)->first();
                if ($warehouse) {
                    $this->warehouse_id = $warehouse->getKey();
                }
            }
        }

        return parent::save($options);
    }

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
