<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use phpseclib\Math\BigInteger;

/**
 * @property BigInteger order_id
 * @property BigInteger|null product_id
 * @property string|null sku_ordered
 * @property string|null name_ordered
 * @property float quantity_ordered
 * @property float quantity_picked
 */
class OrderProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sku_ordered',
        'name_ordered',
        'price',
        'quantity_ordered',
        'quantity_picked',
    ];

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
