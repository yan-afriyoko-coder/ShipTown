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
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * App\Models\Inventory.
 *
 * @property int         $id
 * @property int|null    $warehouse_id
 * @property int         $product_id
 * @property int         $location_id
 * @property string      $warehouse_code
 * @property string      $shelve_location
 * @property float       $quantity
 * @property float       $quantity_reserved
 * @property float       $quantity_incoming
 * @property float       $restock_level
 * @property float       $reorder_point
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
 * @method static Builder|Inventory whereWarehouseCode($value)
 * @method static Inventory firstOrNew(array $array)
 *
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @mixin Eloquent
 */
class Inventory extends BaseModel
{
    use LogsActivityTrait;

    protected $table = 'inventory';

    protected static $logAttributes = [
        'quantity',
        'quantity_reserved',
        'restock_level',
        'reorder_point',
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
        'quantity_incoming',
        'restock_level',
        'reorder_point',
    ];

    protected $attributes = [
        'quantity'          => 0,
        'quantity_reserved' => 0,
        'quantity_incoming' => 0,
        'restock_level'     => 0,
        'reorder_point'     => 0,
    ];

    protected $casts = [
        'quantity'           => 'float',
        'quantity_reserved'  => 'float',
        'quantity_available' => 'float',
        'quantity_incoming'  => 'float',
        'restock_level'      => 'float',
        'reorder_point'      => 'float',
        'quantity_required'  => 'float',
    ];

    /**
     * @return QueryBuilder
     */
    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(Inventory::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('product_id'),
                AllowedFilter::exact('warehouse_id'),
                AllowedFilter::exact('warehouse_code'),
                AllowedFilter::exact('quantity_incoming'),
                AllowedFilter::scope('quantity_available_between'),
                AllowedFilter::scope('quantity_required_between'),
                AllowedFilter::scope('restock_level_between'),

                AllowedFilter::scope('source_warehouse_code', 'addWarehouseSource'),
                AllowedFilter::scope('inventory_source_quantity_available_between'),
            ])
            ->allowedSorts([
                'id',
                'product_id',
                'warehouse_id',
                'quantity_available',
                'restock_level',
                'quantity_required',
                'quantity_incoming',
            ])
            ->allowedIncludes([
                'product'
            ]);
    }

    /**
     * @return float
     */
    public function getQuantityAvailableAttribute(): float
    {
        // quantity_available is mssql computed stored value, it's not updated until is saved
        // this is to make sure always up-to-date value is returned
        if ($this->quantity && $this->quantity_reserved) {
            return $this->quantity - $this->quantity_reserved;
        }

        return $this->attributes['quantity_available'];
    }

    /**
     * @return float
     */
    public function getQuantityRequiredAttribute(): float
    {
        // quantity_required is mssql computed stored value, it's not updated until is saved
        // this is to make sure always up-to-date value is returned
        if ($this->quantity === null || $this->quantity_reserved === null || $this->quantity_incoming === null) {
            $quantity_required = $this->restock_level - ($this->quantity_available + $this->quantity_incoming);
            if ($quantity_required > 0) {
                return $quantity_required;
            }

            return 0;
        }

        return $this->attributes['quantity_required'];
    }

    /**
     * @param mixed $query
     * @param mixed $min
     * @param mixed $max
     *
     * @return mixed
     */
    public function scopeQuantityAvailableBetween($query, $min, $max)
    {
        return $query->whereBetween('quantity_available', [floatval($min), floatval($max)]);
    }

    /**
     * @param mixed $query
     * @param mixed $min
     * @param mixed $max
     *
     * @return mixed
     */
    public function scopeQuantityRequiredBetween($query, $min, $max)
    {
        return $query->whereBetween('quantity_required', [floatval($min), floatval($max)]);
    }

    /**
     * @param mixed $query
     * @param mixed $min
     * @param mixed $max
     *
     * @return mixed
     */
    public function scopeRestockLevelBetween($query, $min, $max)
    {
        return $query->whereBetween('restock_level', [floatval($min), floatval($max)]);
    }

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


    /**
     * @param $query
     * @param $warehouse_code
     * @return mixed
     */
    public function scopeAddWarehouseSource($query, $warehouse_code)
    {
        $source_inventory = Inventory::query()
            ->select([
                'warehouse_id as inventory_source_warehouse_id',
                'warehouse_code as inventory_source_warehouse_code',
                'quantity_available as inventory_source_quantity_available',
                'product_id as inventory_source_product_id',
            ])
            ->where(['warehouse_code' => $warehouse_code])
            ->toBase();

        return $query->leftJoinSub($source_inventory, 'inventory_source', function ($join) {
            $join->on('inventory.product_id', '=', 'inventory_source_product_id');
        });
    }

    /**
     * @param mixed $query
     * @param mixed $min
     * @param mixed $max
     *
     * @return mixed
     */
    public function scopeInventorySourceQuantityAvailableBetween($query, $min, $max)
    {
        return $query->whereBetween('inventory_source_quantity_available', [floatval($min), floatval($max)]);
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
