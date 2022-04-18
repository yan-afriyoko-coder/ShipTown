<?php

namespace App\Modules\Reports\src\Models;

use App\Models\Inventory;

class InventoryReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->fields = [
            'inventory.warehouse_code'      => 'warehouse_code',
            'product.sku'                   => 'product_sku',
            'product.name'                  => 'product_name',
            'inventory.quantity_available'  => 'quantity_available',
            'inventory.restock_level'       => 'restock_level',
            'inventory.reorder_point'       => 'reorder_point',
            'inventory.quantity_required'   => 'quantity_required',
        ];

        $this->baseQuery = Inventory::query()
            ->leftJoin('products as product', 'inventory.product_id', '=', 'product.id')
            ->when(request('inventory_source_warehouse_code'), function ($query) {
                $query->leftJoin('inventory as inventory_source', 'inventory.product_id', '=', 'product.id');
            })
            ->toBase();

        $this->casts = [
            'restock_level'      => 'float',
            'reorder_point'      => 'float',
            'quantity_required'  => 'float',
            'quantity_available' => 'float',
        ];
    }
}
