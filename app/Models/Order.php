<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use phpseclib\Math\BigInteger;

/**
 * @property BigInteger id
 * @property BigInteger shipping_address_id
 * @property double total
 * @property double total_paid
 * @property string order_number
 * @property string status_code
 * @property integer product_line_count
 * @property integer total_quantity_ordered
 * @property Carbon|null picked_at
 * @method  self isPicked(bool $expected)
 * @property-read boolean isPaid
 * @method  self whereIsPicked()
 * @method  self whereIsNotPicked()
 * @property Carbon|null packed_at
 * @property Carbon|null deleted_at
 * @property Carbon|null updated_at
 * @property Carbon|null created_at
 */
class Order extends Model
{
    protected $fillable = [
        'order_number',
        'picked_at',
        'shipping_number',
        'shipping_address_id',
        'is_packed',
        'order_placed_at',
        'order_closed_at',
        'status_code',
        'packer_user_id',
        'raw_import',
        'total',
        'total_paid',
    ];

    protected $casts = [
        'raw_import' => 'array',
    ];

    // we use attributes to set default values
    // we wont use database default values
    // as this is then not populated
    // correctly to events
    protected $attributes = [
        'status_code' => 'processing',
        'raw_import' => '{}',
    ];

    protected $appends = [
        'is_picked',
        'is_packed',
    ];

    /**
     * @param Builder $query
     * @param int $inventory_location_id
     * @return Builder
     */
    public function scopeAddInventorySource($query, $inventory_location_id)
    {
        $source_inventory = OrderProduct::query()
            ->select([
                'order_id as order_id',
                DB::raw('min(shelve_location) as min_shelf_location'),
                DB::raw('max(shelve_location) as max_shelf_location'),
            ])
            ->leftJoin('inventory', function ($join) use ($inventory_location_id) {
                $join->on('order_products.product_id', '=', 'inventory.product_id');
                $join->on('inventory.location_id', '=', DB::raw($inventory_location_id));
            })
            ->groupBy('order_products.order_id')
            ->toBase();

        return $query->leftJoinSub($source_inventory, 'inventory_source', function ($join) {
            $join->on('orders.id', '=', 'inventory_source.order_id');
        });
    }

    public function getIsPaidAttribute()
    {
        return ($this->total > 0) && ($this->total === $this->total_paid);
    }

    /**
     * @param $expected
     * @return bool
     */
    public function isNotStatusCode($expected)
    {
        return !$this->isStatusCode($expected);
    }

    /**
     * @param $expected
     * @return bool
     */
    public function isStatusCode($expected)
    {
        return $this->getAttribute('status_code') === $expected;
    }


    /**
     * @param array $statusCodes
     * @return bool
     */
    public function isStatusCodeNotIn(array $statusCodes)
    {
        return !$this->isStatusCodeIn($statusCodes);
    }

    /**
     * @param array $statusCodes
     * @return bool
     */
    public function isStatusCodeIn(array $statusCodes)
    {
        $statusCode = $this->getAttribute('status_code');

        return array_search($statusCode, $statusCodes) > -1;
    }

    /**
     * @param $query
     * @param bool $expected
     * @return self
     */
    public function scopeHasPacker($query, bool $expected)
    {
        if ($expected === false) {
            return $query->whereNull('packer_user_id');
        }

        return $query->whereNotNull('packer_user_id');
    }

    /**
     * @param $query
     * @param bool $expected
     * @return self
     */
    public function scopeIsPicked($query, bool $expected)
    {
        if ($expected === true) {
            return $query->whereIsPicked();
        }

        return $query->whereIsNotPicked();
    }

    /**
     * @param $query
     * @return self
     */
    public function scopeIsPacking($query, $is_packing)
    {
        if ($is_packing) {
            return $query->whereNotNull('packer_user_id');
        }

        return $query->whereNull('packer_user_id');
    }

    /**
     * @param $query
     * @return self
     */
    public function scopeWhereIsPicked($query)
    {
        return $query->whereNotNull('picked_at');
    }

    /**
     * @param $query
     * @return self
     */
    public function scopeWhereIsNotPicked($query)
    {
        return $query->whereNull('picked_at');
    }

    public function scopeIsPacked($query, $value)
    {
        return $query->whereNull('packed_at', 'and', $value);
    }

    public function getIsPackedAttribute()
    {
        return $this->packed_at !== null;
    }

    public function setIsPackedAttribute($value)
    {
        $this->packed_at = $value ? now() : null;
    }

    public function getIsPickedAttribute()
    {
        return $this->picked_at !== null;
    }

    public function setIsPickedAttribute($value)
    {
        $this->picked_at = $value ? now() : null;
    }

    public function scopePacklist($query, $inventory_id)
    {
        return $this->hasOne(Packlist::class)->test(100);
    }

    public function scopeActive($query)
    {
        return $query->where('status_code', '=', 'processing');
    }

    /**
     * @return HasMany | OrderProduct
     */
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * @return HasMany | Packlist
     */
    public function packlist()
    {
        return $this->hasMany(Packlist::class);
    }

    /**
     * @return BelongsTo | OrderAddress
     */
    public function shippingAddress()
    {
        return $this->belongsTo(OrderAddress::class);
    }

    /**
     * @return BelongsTo | User
     */
    public function packer()
    {
        return $this->belongsTo(User::class, 'packer_user_id');
    }

    /**
     * @return HasMany | OrderShipment
     */
    public function orderShipments()
    {
        return $this->hasMany(OrderShipment::class);
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function stats()
    {
        return $this->hasOne(OrderStats::class);
    }
}
