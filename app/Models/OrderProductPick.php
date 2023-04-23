<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $pick_id
 * @property int $order_product_id
 * @property double $quantity_picked
 * @property double $quantity_skipped_picking
 */
class OrderProductPick extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'picks_orders_products';

    protected $fillable = [
        'pick_id',
        'order_product_id',
        'quantity_picked',
        'quantity_skipped_picking',
    ];
}
