<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int order_id
 * @property-read int product_line_count
 * @property-read float quantity_ordered_sum
 * @property-read float quantity_to_ship_sum
 * @property-read float total_ordered
 */
class OrderProductTotal extends Model
{
    protected $table = 'orders_products_totals';

    protected $fillable = [
        'order_id',
        'count',
        'quantity_ordered',
        'quantity_split',
        'quantity_picked',
        'quantity_skipped_picking',
        'quantity_not_picked',
        'quantity_shipped',
    ];
}
