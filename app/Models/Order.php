<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'order_as_json'
    ];

    protected $casts = [
        'order_as_json' => 'array'
    ];
}
