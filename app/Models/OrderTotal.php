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
class OrderTotal extends Model
{
    protected $table = 'orders_totals_view';

    protected $casts = [
        'product_line_count'    => 'integer',
        'quantity_ordered_sum'  => 'float',
        'quantity_to_ship_sum'  => 'float',
        'total_ordered'         => 'float',
    ];
}
