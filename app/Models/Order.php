<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'order_placed_at',
        'order_closed_at',
        'products_count',
        'status_code'
    ];

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }
}
