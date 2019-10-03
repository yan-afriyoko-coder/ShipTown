<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'order_json'
    ];

    protected $casts = [
        'order_json' => 'array'
    ];
}
