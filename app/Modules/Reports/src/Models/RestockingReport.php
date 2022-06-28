<?php

namespace App\Modules\Reports\src\Models;

use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class RestockingReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Restocking Report';

        $this->baseQuery = Inventory::query()
            ->leftJoin('inventory as inventory_source', function ($join) {
                $join->on('inventory_source.product_id', '=', 'inventory.product_id');
            })
            ->leftJoin('products as product', 'inventory.product_id', '=', 'product.id');

        $this->fields = [
            'warehouse_code'                     => 'inventory.warehouse_code',
            'product_sku'                        => 'product.sku',
            'product_name'                       => 'product.name',
            'reorder_point'                      => 'inventory.reorder_point',
            'restock_level'                      => 'inventory.restock_level',
            'quantity_available'                 => 'inventory.quantity_available',
            'quantity_incoming'                  => 'inventory.quantity_incoming',
            'quantity_required'                  => 'inventory.quantity_required',
            'warehouse_quantity'                 => 'inventory_source.quantity_available',
            'inventory_source_warehouse_code'    => 'inventory_source.warehouse_code',
            'quantity_to_ship'                   => DB::raw('CASE WHEN inventory_source.quantity_available < inventory.quantity_required THEN inventory_source.quantity_available ELSE inventory.quantity_required END as quantity_to_ship'),
        ];

        $this->casts = [
            'restock_level'      => 'float',
            'reorder_point'      => 'float',
            'quantity_required'  => 'float',
            'quantity_available' => 'float',
            'quantity_incoming'  => 'float',
            'warehouse_quantity' => 'float',
        ];

        $this->addFilter(
            AllowedFilter::callback('has_tags', function ($query, $value) {
                $query->whereHas('product', function ($query) use ($value) {
                    $query->withAllTags($value);
                });
            })
        );
    }
}
