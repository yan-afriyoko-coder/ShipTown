<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pick extends Model
{
    protected $fillable = [
        'product_id',
        'sku_ordered',
        'name_ordered',
        'quantity_required'
    ];
}
