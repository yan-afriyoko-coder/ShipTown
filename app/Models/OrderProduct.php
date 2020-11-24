<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use phpseclib\Math\BigInteger;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @property BigInteger order_id
 * @property BigInteger|null product_id
 * @property string|null sku_ordered
 * @property string|null name_ordered
 * @property float quantity_ordered
 * @property float quantity_to_pick
 * @property float quantity_picked
 * @property float quantity_not_picked
 * @property float quantity_shipped
 */
class OrderProduct extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected static $logAttributes = [
        'quantity_ordered',
        'quantity_picked',
        'quantity_skipped_picking',
        'quantity_not_picked',
        'quantity_shipped',
    ];

    protected $fillable = [
        'order_id',
        'product_id',
        'sku_ordered',
        'name_ordered',
        'price',
        'quantity_ordered',
        'quantity_picked',
        'quantity_skipped_picking',
        'quantity_not_picked',
        'quantity_shipped',
    ];

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $this->quantity_to_pick = $this->quantity_ordered - $this->quantity_picked - $this->quantity_skipped_picking - $this->quantity_not_picked;
        return parent::save($options);
    }

    /**
     * @return QueryBuilder
     */
    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(OrderProduct::class)
            ->allowedFilters([
                AllowedFilter::scope('inventory_source_location_id', 'addInventorySource')->default(100),
                AllowedFilter::scope('in_stock_only', 'whereInStock'),

                AllowedFilter::scope('not_picked_only', 'whereNotPicked'),

                AllowedFilter::exact('product_id'),
                AllowedFilter::exact('order_id'),

                AllowedFilter::scope('current_shelf_location', 'MinimumShelfLocation'),
                AllowedFilter::exact('order.status_code')->ignore(''),
            ])
            ->allowedIncludes([
                'order',
                'product',
                'product.aliases',
            ])
            ->allowedSorts([
                'inventory_source_shelf_location',
                'sku_ordered',
                'id',
            ]);
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
     * @param boolean $in_stock
     * @return mixed
     */
    public function scopeWhereInStock($query, $in_stock)
    {
        if (!$in_stock) {
            return $query;
        }

        return $query->where('inventory_source.inventory_source_quantity', '>', 0);
    }

    /**
     * @param Builder $query
     * @param int $inventory_location_id
     * @return Builder
     */
    public function scopeAddInventorySource($query, $inventory_location_id)
    {
        $source_inventory = Inventory::query()
            ->select([
                'location_id as inventory_source_location_id',
                'shelve_location as inventory_source_shelf_location',
                'quantity as inventory_source_quantity',
                'product_id as inventory_source_product_id',
            ])
            ->where(['location_id'=>$inventory_location_id])
            ->toBase();

        return $query->leftJoinSub($source_inventory, 'inventory_source', function ($join) {
            $join->on('order_products.product_id', '=', 'inventory_source_product_id');
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
