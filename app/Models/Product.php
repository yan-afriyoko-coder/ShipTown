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

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->user_id = auth()->id();
    }
}
