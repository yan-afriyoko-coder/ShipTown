<?php

namespace App\Modules\Reports\src\Models;

use App\Models\Inventory;
use App\Models\Warehouse;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class RestockingReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Restocking Report';
        if (request('title')) {
            $this->report_name = request('title').' ('.$this->report_name.')';
        }
        $this->view = 'reports.restocking-report';

        $this->defaultSelect = implode(',', [
            'inventory_id',
//            'product_id',
//            'inventory_id',
//            'warehouse_id',
            'warehouse_code',
//            'product_sku',
//            'product_name',
            'quantity_required',
            'quantity_in_stock',
            'quantity_available',
            'quantity_incoming',
            'reorder_point',
            'restock_level',
//            'warehouse_quantity',
//            'warehouse_has_stock',
            'first_sold_at',
            'last_sold_at',
            'first_received_at',
            'last_received_at',
            'last_movement_at',
            'last_counted_at',
        ]);

        $this->defaultSort = '-quantity_required';

        $this->allowedIncludes = ['product', 'product.tags'];

        $this->baseQuery = Inventory::query()
//            ->leftJoin('products', 'inventory.product_id', '=', 'products.id')
            ->leftJoin('inventory as inventory_source', function (JoinClause $join) {
                $join->on('inventory_source.product_id', '=', 'inventory.product_id');
                $join->where('inventory_source.warehouse_id', '=', DB::raw(2));
                $join->whereBetween('inventory_source.quantity_available', [0.01, 9999999]);
            });

        $this->fields = [
            'id'                                 => 'inventory.id',
//            'product_id'                         => 'products.id',
//            'product_sku'                        => 'products.sku',
//            'product_name'                       => 'products.name',
            'inventory_id'                       => 'inventory.id',
            'warehouse_id'                       => 'inventory.warehouse_id',
            'warehouse_code'                     => 'inventory.warehouse_code',
            'reorder_point'                      => 'inventory.reorder_point',
            'restock_level'                      => 'inventory.restock_level',
            'quantity_in_stock'                  => 'inventory.quantity',
            'quantity_available'                 => 'inventory.quantity_available',
            'quantity_incoming'                  => 'inventory.quantity_incoming',
            'quantity_required'                  => 'inventory.quantity_required',
            'last_movement_at'                   => 'inventory.last_movement_at',
            'last_sold_at'                       => 'inventory.last_sold_at',
            'first_sold_at'                      => 'inventory.first_sold_at',
            'last_counted_at'                    => 'inventory.last_counted_at',
            'first_received_at'                  => 'inventory.first_received_at',
            'last_received_at'                   => 'inventory.last_received_at',
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
//            'warehouse_quantity' => 'float',
//            'warehouse_has_stock'=> 'boolean',
//            'quantity_to_ship'   => 'float',
            "last_movement_at"   => 'datetime',
            'last_counted_at'    => 'datetime',
        ];

//        $this->addFilter(
//            AllowedFilter::callback('has_tags', function ($query, $value) {
//                return $query->whereIn(
//                    'inventory_source.warehouse_id',
//                    Warehouse::withAllTags(explode(',', $value))->pluck('id')
//                );
//            })
//        );

        $this->addFilter(
            AllowedFilter::callback('product_has_tags', function ($query, $value) {
                return $query->hasTags($value);
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
