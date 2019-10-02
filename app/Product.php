<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        "sku"
    ];

    protected $casts = [
        'quantity_reserved_details' => 'array'
    ];
}
