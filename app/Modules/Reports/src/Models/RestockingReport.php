<?php

namespace App\Modules\Reports\src\Models;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\Tags\Tag;

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
            'product_id',
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
            'first_sold_at',
            'last_sold_at',
            'first_received_at',
            'last_received_at',
            'last_movement_at',
            'last_counted_at',
        ]);

        $this->defaultSort = '-quantity_required';

        $this->allowedIncludes = ['product', 'product.tags', 'product.prices'];

        $warehouseIds = Warehouse::withAnyTagsOfAnyType('fulfilment')->get('id');

        $this->baseQuery = Inventory::query()
            ->leftJoin('products', 'inventory.product_id', '=', 'products.id')
            ->join('inventory as inventory_source', function (JoinClause $join) use ($warehouseIds) {
                $join->on('inventory_source.product_id', '=', 'inventory.product_id');
                $join->whereIn('inventory_source.warehouse_id', $warehouseIds);
                $join->where('inventory_source.is_in_stock', '=', 1);
            })->withSum('last7daysSales', 'quantity_delta');

        $this->fields = [
            'id'                                 => 'inventory.id',
            'product_id'                         => 'inventory.product_id',
            'product_sku'                        => 'products.sku',
            'product_name'                       => 'products.name',
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
            'warehouse_quantity'                 => DB::raw('IFNULL(inventory_source.quantity_available, 0)'),
//            'warehouse_has_stock'                => DB::raw('ISNULL(inventory_source.quantity_available)'),
            'inventory_source_warehouse_code'    => 'inventory_source.warehouse_code',
            'warehouse_has_stock'    => 'inventory_source.is_in_stock',
        ];

        $this->casts = [
            'warehouse_id'       => 'integer',
            'product_name'       => 'string',
            'warehouse_code'     => 'string',
            'restock_level'      => 'float',
            'reorder_point'      => 'float',
            'quantity_required'  => 'float',
            'quantity_in_stock'  => 'float',
            'quantity_available' => 'float',
            'quantity_incoming'  => 'float',
            'warehouse_quantity' => 'float',
            "last_movement_at"   => 'datetime',
            'last_counted_at'    => 'datetime',
            'warehouse_has_stock'=> 'boolean',
        ];

        $this->addFilter(
            AllowedFilter::callback('product_has_tags_containing', function ($query, $value) {
                $tags = Tag::containing($value)->get('id');

                $productsQuery = Product::withAnyTags($tags)->select('id');
                return $query->whereIn('inventory.product_id', $productsQuery);
            })
        );

        $this->addFilter(
            AllowedFilter::callback('product_has_tags', function ($query, $value) {
                $tags = Tag::findFromStringOfAnyType($value);

                $productsQuery = Product::withAnyTags($tags)->select('id');
                return $query->whereIn('inventory.product_id', $productsQuery);
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
