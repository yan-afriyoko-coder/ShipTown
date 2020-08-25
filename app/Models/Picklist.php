<?php

namespace App\Models;

use DateTime;
use Hulkur\HasManyKeyBy\HasManyKeyByRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * @property float quantity_picked
 * @property DateTime|null picked_at
 * @property Order|null order
 */
class Picklist extends Model
{
    use SoftDeletes;
    use HasManyKeyByRelationship;

    protected $fillable = [
        'order_id',
        'order_product_id',
        'product_id',
        'location_id',
        'sku_ordered',
        'name_ordered',
        'quantity_requested',
        'quantity_picked',
        'picker_user_id',
        'picked_at',
        'is_picked',
    ];

    protected $appends = [
        'is_picked'
    ];

    /**
     * @param Builder $query
     * @param int $inventory_location_id
     * @return Builder
     */
    public function scopeInventorySource($query, $inventory_location_id)
    {
        $source_inventory = Inventory::query()
            ->select([
                'shelve_location as inventory_source_shelf_location',
                'quantity as inventory_source_quantity',
                'product_id as inventory_source_product_id',
            ])
            ->where(['location_id'=>$inventory_location_id])
            ->toBase();

        return $query->leftJoinSub($source_inventory, 'inventory_source', function ($join) {
            $join->on('picklists.product_id', '=', 'inventory_source_product_id');
        });
    }

    /**
     * @param Builder $query
     * @param boolean $in_stock_only
     * @return mixed
     */
    public function scopeInStockOnly($query, $in_stock_only)
    {
        if (!$in_stock_only) {
            return $query;
        }

        return $query->where('inventory_source.inventory_source_quantity', '>', 0);
    }

    /**
     * @param Builder $query
     * @param string $currentLocation
     * @return Builder
     */
    public function scopeMinimumShelfLocation($query, $currentLocation)
    {
        return $query->where('inventory_source.inventory_source_shelf_location', '>=', $currentLocation);
    }

    /**
     * @param Builder $query
     * @param boolean $value
     * @return mixed
     */
    public function scopeWhereIsSingleLineOrder($query, $value)
    {
        if (!$value) {
            return $query;
        }

        return $query->whereHas('order', function ($query) {
            return $query->where('product_line_count', '=', 1);
        });
    }

    public static function addPick($params)
    {
        return self::query()->create($params);
    }

    public function getIsPickedAttribute()
    {
        return $this->picked_at !== null;
    }

    public function setIsPickedAttribute($value)
    {
        $this->picked_at = $value ? now() : null;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function inventory()
    {
        return $this->hasMany(
            Inventory::class,
            'product_id',
            'product_id'
        )->keyBy('location_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'picker_user_id');
    }

    public function wasPickSkipped()
    {
        return $this->quantity_picked == 0 && $this->picked_at != null;
    }
}
