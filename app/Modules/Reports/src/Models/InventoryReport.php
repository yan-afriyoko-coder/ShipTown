<?php

namespace App\Modules\Reports\src\Models;

use App\Models\Inventory;

class InventoryReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Inventory Report';

        $this->fields = [
            'warehouse_code'        => 'inventory.warehouse_code',
            'product_sku'           => 'product.sku',
            'product_name'          => 'product.name',
            'quantity'              => 'inventory.quantity',
            'quantity_reserved'     => 'inventory.quantity_reserved',
            'quantity_available'    => 'inventory.quantity_available',
            'quantity_incoming'     => 'inventory.quantity_incoming',
            'restock_level'         => 'inventory.restock_level',
            'reorder_point'         => 'inventory.reorder_point',
            'quantity_required'     => 'inventory.quantity_required',
        ];

        $this->baseQuery = Inventory::query()
            ->leftJoin('products as product', 'inventory.product_id', '=', 'product.id');

        $this->casts = [
            'quantity'              => 'float',
            'quantity_reserved'     => 'float',
            'quantity_available'    => 'float',
            'quantity_incoming'     => 'float',
            'restock_level'         => 'float',
            'reorder_point'         => 'float',
        ];
    }
}
