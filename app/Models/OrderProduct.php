<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $fillable = [
        'product_id',
        'order_product_id',
        'model',
        'name',
        'price',
        'price_inc_tax',
        'quantity',
        'discount_amount',
        'total_price',
        'tax_percent',
        'tax_value',
        'tax_value_after_discount',
        'variant_id',
        'weight_unit',
        'weight',
        'parent_order_product_id',
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
}
