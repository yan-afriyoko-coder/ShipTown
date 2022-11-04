<?php

namespace App\Modules\Reports\src\Models;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class RestockingReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->view = 'reports.restocking-report';

        $this->report_name = 'Restocking Report';

        $this->defaultSelect = implode(',', [
            'id',
            'product_id',
            'inventory_id',
            'warehouse_code',
            'product_sku',
            'product_name',
            'quantity_required',
            'quantity_in_stock',
            'quantity_available',
            'quantity_incoming',
            'reorder_point',
            'restock_level',
            'warehouse_quantity',
            'warehouse_has_stock',
            'last_counted_at',
        ]);

        $this->defaultSort = '-quantity_required';

        $this->allowedIncludes = ['tags'];

        if (request('title')) {
            $this->report_name = request('title').' ('.$this->report_name.')';
        }

        $this->baseQuery = Product::query()
            ->leftJoin('inventory', 'inventory.product_id', '=', 'products.id')
            ->leftJoin('inventory as inventory_source', 'inventory_source.product_id', '=', 'inventory.product_id');

        $this->fields = [
            'id'                                 => 'products.id',
            'product_id'                         => 'products.id',
            'inventory_id'                       => 'inventory.id',
            'warehouse_code'                     => 'inventory.warehouse_code',
            'product_sku'                        => 'products.sku',
            'product_name'                       => 'products.name',
            'reorder_point'                      => 'inventory.reorder_point',
            'restock_level'                      => 'inventory.restock_level',
            'quantity_in_stock'                  => 'inventory.quantity',
            'quantity_available'                 => 'inventory.quantity_available',
            'quantity_incoming'                  => 'inventory.quantity_incoming',
            'quantity_required'                  => 'inventory.quantity_required',
            'last_counted_at'                    => 'inventory.last_counted_at',
            'warehouse_quantity'                 => 'inventory_source.quantity_available',
            'warehouse_has_stock'                => DB::raw('inventory_source.quantity_available > 0'),
            'inventory_source_warehouse_code'    => 'inventory_source.warehouse_code',
            'quantity_to_ship'                   => DB::raw("CASE WHEN inventory_source.quantity_available < inventory.quantity_required THEN inventory_source.quantity_available ELSE inventory.quantity_required END"),
        ];

        $this->casts = [
            'product_name'       => 'string',
            'warehouse_code'     => 'string',
            'restock_level'      => 'float',
            'reorder_point'      => 'float',
            'quantity_required'  => 'float',
            'quantity_in_stock'  => 'float',
            'quantity_available' => 'float',
            'quantity_incoming'  => 'float',
            'warehouse_quantity' => 'float',
            'warehouse_has_stock'=> 'boolean',
            'quantity_to_ship'   => 'float',
            'last_counted_at'    => 'datetime',
        ];

        $this->addFilter(
            AllowedFilter::callback('has_tags', function ($query, $value) {
                return $query->withAllTags(
                    'inventory_source.warehouse_id',
                    Warehouse::withAllTags(explode(',', $value))->pluck('id')
                );
            })
        );

        $this->addFilter(
            AllowedFilter::callback('search', function ($query, $value) {
                $query->where(function ($query) use ($value) {
                    $query->where('products.sku', 'like', '%'.$value.'%')
                        ->orWhere('products.name', 'like', '%'.$value.'%');
                });
            })
        );
    }
}
