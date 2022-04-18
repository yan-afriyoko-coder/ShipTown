<?php

namespace App\Modules\Reports\src\Models;

use App\Models\Inventory;

class RestockingReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->fields = [
            'inventory.warehouse_code'              => 'warehouse_code',
            'product.sku'                           => 'product_sku',
            'product.name'                          => 'product_name',
            'inventory.quantity_available'          => 'quantity_available',
            'inventory.restock_level'               => 'restock_level',
            'inventory.reorder_point'               => 'reorder_point',
            'inventory.quantity_required'           => 'quantity_required',
            'inventory_source.warehouse_code'       => 'inventory_source_warehouse_code',
            'inventory_source.quantity_available'   => 'warehouse_quantity',
        ];

        $this->baseQuery = Inventory::query()
            ->leftJoin('products as product', 'inventory.product_id', '=', 'product.id')
            ->leftJoin('inventory as inventory_source', function ($leftJoin) {
                $leftJoin->on('inventory.product_id', '=', 'product.id');
                $leftJoin->where([
                    'inventory.warehouse_code' => data_get(request('filter'), 'inventory_source_warehouse_code')
                ]);
            })
            ->toBase();

        $this->casts = [
            'restock_level'      => 'float',
            'reorder_point'      => 'float',
            'quantity_required'  => 'float',
            'quantity_available' => 'float',
            'warehouse_quantity' => 'float',
        ];
    }
}
