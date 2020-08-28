<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickRequest extends Model
{
    protected $fillable = [
        'order_product_id',
        'quantity_required',
    ];
}
