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
        'quantity_to_pick',
        'quantity_to_ship',
    ];

    protected $casts = [
        'count'                   => 'integer',
        'quantity_ordered'        => 'float',
        'quantity_split'          => 'float',
        'quantity_picked'         => 'float',
        'quantity_skipped_picking'=> 'float',
        'quantity_not_picked'     => 'float',
        'quantity_shipped'        => 'float',
        'quantity_to_pick'        => 'float',
        'quantity_to_ship'        => 'float',
    ];

    protected $attributes = [
        'count'                   => 0,
        'quantity_ordered'        => 0,
        'quantity_split'          => 0,
        'quantity_picked'         => 0,
        'quantity_skipped_picking'=> 0,
        'quantity_not_picked'     => 0,
        'quantity_shipped'        => 0,
        'quantity_to_pick'        => 0,
        'quantity_to_ship'        => 0,
    ];
}
