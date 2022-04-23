<?php

namespace App\Modules\Reports\src\Models;

use App\Models\Inventory;

class RestockingReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->fields = [
           'warehouse_code'                     => 'inventory.warehouse_code',
           'product_sku'                        => 'product.sku',
           'product_name'                       => 'product.name',
           'quantity_available'                 => 'inventory.quantity_available',
           'restock_level'                      => 'inventory.restock_level',
           'reorder_point'                      => 'inventory.reorder_point',
           'quantity_required'                  => 'inventory.quantity_required',
           'inventory_source_warehouse_code'    => 'inventory_source.warehouse_code',
           'warehouse_quantity'                 => 'inventory_source.quantity_available',
        ];

        $this->baseQuery = Inventory::query()
            ->leftJoin('products as product', 'inventory.product_id', '=', 'product.id')
            ->leftJoin('inventory as inventory_source', function ($join) {
                $join->on('inventory.product_id', '=', 'product.id');
            });

        $this->casts = [
            'restock_level'      => 'float',
            'reorder_point'      => 'float',
            'quantity_required'  => 'float',
            'quantity_available' => 'float',
            'warehouse_quantity' => 'float',
        ];
    }
}
