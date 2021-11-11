<?php

namespace App\Modules\BoxTop\src\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string Warehouse
 *
 */
class WarehouseStock extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules_boxtop_warehouse_stock';

    /**
     * @var string[]
     */
    protected $fillable = [
        'SKUGroup',
        'SKUNumber',
        'SKUName',
        'Warehouse',
        'Attributes',
        'WarehouseQuantity',
        'Allocated',
        'Available',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'Attributes' => 'array'
    ];
}
