<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        "sku"
    ];

    protected $attributes = [
        'quantity_reserved' => 0,
    ];

}
