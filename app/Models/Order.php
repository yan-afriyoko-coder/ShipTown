<?php

namespace App\Models;

use App\Services\PicklistService;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use phpseclib\Math\BigInteger;

/**
 * @property BigInteger id
 * @property BigInteger shipping_address_id
 * @property string order_number
 * @property string status_code
 * @property integer product_line_count
 * @property integer total_quantity_ordered
 * @property Carbon|null picked_at
 * @method  self isPicked(bool $expected)
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
        'shipping_address_id',
        'order_placed_at',
        'order_closed_at',
        'status_code',
        'raw_import'
    ];

    protected $casts = [
        'raw_import' => 'array',
    ];

    // we use attributes to set default values
    // we wont use database default values
    // as this is then not populated
    // correctly to events
    protected $attributes = [
        'raw_import' => '{}',
    ];

    protected $appends = [
        'is_picked',
        'is_packed',
    ];

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
     * @return BelongsTo | OrderAddress
     */
    public function shippingAddress()
    {
        return $this->belongsTo(OrderAddress::class);
    }
}
