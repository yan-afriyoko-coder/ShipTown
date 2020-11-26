<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * App\Models\OrderProduct
 *
 * @property int $id
 * @property int|null $order_id
 * @property int|null $product_id
 * @property string $sku_ordered
 * @property string $name_ordered
 * @property string $price
 * @property string $quantity_ordered
 * @property string $quantity_to_pick
 * @property string $quantity_picked
 * @property string $quantity_skipped_picking
 * @property string $quantity_not_picked
 * @property string $quantity_shipped
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read Order|null $order
 * @property-read Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct addInventorySource($inventory_location_id)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct minimumShelfLocation($currentLocation)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct newQuery()
 * @method static Builder|OrderProduct onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereInStock($in_stock)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereNameOrdered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereQuantityNotPicked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereQuantityOrdered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereQuantityPicked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereQuantityShipped($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereQuantitySkippedPicking($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereQuantityToPick($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereSkuOrdered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereStatusCodeIn($statusCodeArray)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereUpdatedAt($value)
 * @method static Builder|OrderProduct withTrashed()
 * @method static Builder|OrderProduct withoutTrashed()
 * @mixin Eloquent
 */
class OrderProduct extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected static $logAttributes = [
        'quantity_ordered',
        'quantity_to_pick',
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
        $this->quantity_to_pick = $this->quantity_ordered - $this->quantity_picked - $this->quantity_skipped_picking;
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
     * @param array $statusCodeArray
     * @return Builder
     */
    public function scopeWhereStatusCodeIn($query, $statusCodeArray)
    {
        return $query->whereHas('order', function ($query) use ($statusCodeArray) {
            $query->whereIn('status_code', $statusCodeArray);
        });
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
