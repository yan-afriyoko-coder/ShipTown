<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $fillable = [
        'sku_ordered',
        'name_ordered',
        'price',
        'quantity',
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
