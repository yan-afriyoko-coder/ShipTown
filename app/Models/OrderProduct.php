<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
