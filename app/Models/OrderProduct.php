<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpseclib\Math\BigInteger;

/**
 * @property BigInteger|null product_id
 * @property string|null sku_ordered
 * @property string|null name_ordered
 * @property float quantity_ordered
 */
class OrderProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sku_ordered',
        'name_ordered',
        'price',
        'quantity_ordered',
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($orderProduct) {
            $orderProduct->options->delete();
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function options()
    {
        return $this->hasMany(OrderProductOption::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
