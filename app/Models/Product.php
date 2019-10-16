<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        "sku",
        "name",
        "price",
        "sale_price",
        "sale_price_start_date",
        "sale_price_end_date",
        "quantity",
    ];

    protected $attributes = [
        'quantity_reserved' => 0,
    ];

}
