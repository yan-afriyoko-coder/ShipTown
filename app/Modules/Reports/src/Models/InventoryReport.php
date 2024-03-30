<?php

namespace App\Modules\Reports\src\Models;

use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class InventoryReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Inventory';

        $this->baseQuery = Inventory::query()
            ->leftJoin('products as product', 'inventory.product_id', '=', 'product.id')
            ->leftJoin('products_prices as product_prices', function ($join) {
                $join->on('inventory.product_id', '=', 'product_prices.product_id')
                    ->on('inventory.warehouse_id', '=', 'product_prices.warehouse_id');
            });

        $this->defaultSelect = implode(',', [
            'warehouse_code',
            'product_sku',
            'product_name',
            'department',
            'shelf_location',
            'quantity',
            'retail_value',
            'cost_value',
            'quantity_reserved',
            'quantity_available',
            'quantity_incoming',
            'quantity_required',
            'reorder_point',
            'restock_level',
            'last_movement_at',
            'last_received_at',
            'last_sold_at',
            'last_counted_at',
        ]);

        $this->fields = [
            'product_sku'           => 'product.sku',
            'product_name'          => 'product.name',
            'department'            => 'product.department',
            'category'              => 'product.category',
            'id'                    => 'inventory.id',
            'warehouse_id'          => 'inventory.warehouse_id',
            'product_id'            => 'inventory.product_id',
            'warehouse_code'        => 'inventory.warehouse_code',
            'shelf_location'        => 'inventory.shelve_location',
            'recount_required'      => 'inventory.recount_required',
            'quantity_available'    => 'inventory.quantity_available',
            'quantity'              => 'inventory.quantity',
            'quantity_reserved'     => 'inventory.quantity_reserved',
            'quantity_incoming'     => 'inventory.quantity_incoming',
            'quantity_required'     => 'inventory.quantity_required',
            'reorder_point'         => 'inventory.reorder_point',
            'restock_level'         => 'inventory.restock_level',
            'last_sequence_number'  => 'inventory.last_sequence_number',
            'first_movement_at'     => 'inventory.first_movement_at',
            'last_movement_at'      => 'inventory.last_movement_at',
            'first_received_at'     => 'inventory.first_received_at',
            'last_received_at'      => 'inventory.last_received_at',
            'first_sold_at'         => 'inventory.first_sold_at',
            'last_sold_at'          => 'inventory.last_sold_at',
            'first_counted_at'      => 'inventory.first_counted_at',
            'last_counted_at'       => 'inventory.last_counted_at',
            'last_movement_id'      => 'inventory.last_movement_id',
            'deleted_at'            => 'inventory.deleted_at',
            'created_at'            => 'inventory.created_at',
            'updated_at'            => 'inventory.updated_at',
            'price'                 => 'products_prices.price',
            'cost'                  => 'products_prices.cost',
            'sale_price'            => 'products_prices.sale_price',
            'sale_start_date'       => 'products_prices.sale_price_start_date',
            'sale_end_date'         => 'products_prices.sale_price_end_date',
            'retail_value'          => DB::raw('ROUND(product_prices.price * inventory.quantity, 2)'),
            'cost_value'            => DB::raw('ROUND(product_prices.cost * inventory.quantity, 2)'),
        ];

        $this->casts = [
            'id'                    => 'integer',
            'warehouse_id'          => 'integer',
            'product_id'            => 'integer',
            'price'                 => 'float',
            'cost'                  => 'float',
            'sale_price'            => 'float',
            'sale_start_date'       => 'datetime',
            'sale_end_date'         => 'datetime',
            'retail_value'          => 'float',
            'cost_value'            => 'float',
            'warehouse_code'        => 'string',
            'shelf_location'        => 'string',
            'recount_required'      => 'string',
            'quantity_available'    => 'float',
            'quantity'              => 'float',
            'quantity_reserved'     => 'float',
            'quantity_incoming'     => 'float',
            'quantity_required'     => 'float',
            'reorder_point'         => 'float',
            'restock_level'         => 'float',
            'last_sequence_number'  => 'integer',
            'first_movement_at'     => 'datetime',
            'last_movement_at'      => 'datetime',
            'first_received_at'     => 'datetime',
            'last_received_at'      => 'datetime',
            'first_sold_at'         => 'datetime',
            'last_sold_at'          => 'datetime',
            'first_counted_at'      => 'datetime',
            'last_counted_at'       => 'datetime',
            'last_movement_id'      => 'datetime',
            'deleted_at'            => 'datetime',
            'created_at'            => 'datetime',
            'updated_at'            => 'datetime',
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
