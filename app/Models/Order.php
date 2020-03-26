<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'order_as_json',
        'originalJson'
    ];

    protected $casts = [
        'order_as_json' => 'array',
        'originalJson' => 'array'
    ];

    // we use attributes to set default values
    // we wont use database default values
    // as this is then not populated
    // correctly to events
    protected $attributes = [
        'order_as_json' => '{}',
        'originalJson' => '{}',
    ];
}
