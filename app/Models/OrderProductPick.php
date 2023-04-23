<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductPick extends Model
{
    use HasFactory;

    protected $table = 'picks_order_products';

    protected $fillable = [
        'pick_id',
        'order_product_id',
        'quantity_picked',
        'quantity_skipped_picking',
    ];
}
