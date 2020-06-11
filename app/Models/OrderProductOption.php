<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProductOption extends Model
{
    protected $fillable = [
        'option_id',
        'name',
        'value',
        'price',
        'weight',
        'type',
        'product_option_value_id',
        'additional_fields',
        'custom_fields',
    ];

    protected $casts = [
        'additional_fields' => 'array',
        'custom_fields' => 'array'
    ];

    public function orderProduct()
    {
        return $this->belongsTo(orderProduct::class);
    }
}
