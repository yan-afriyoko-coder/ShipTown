<?php

namespace App\Modules\Reports\src\Models;

use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class InventoryDashboardReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Inventory Dashboard';

        if (request('title')) {
            $this->report_name = request('title').' ('.$this->report_name.')';
        }

        $this->baseQuery = Inventory::query()
            ->leftJoin('inventory as inventory_source', function ($join) {
                $join->on('inventory_source.product_id', '=', 'inventory.product_id');
            })
            ->leftJoin('products as product', 'inventory.product_id', '=', 'product.id')
            ->where('inventory_source.warehouse_code', '=', '99')
            ->where('inventory_source.quantity_available', '>', 0)
            ->whereNotIn('inventory.warehouse_code', ['99','999','100'])
            ->groupBy('inventory.warehouse_code');

        $this->fields = [
            'warehouse_code'             => 'inventory.warehouse_code',
            'products_on_minus'          => DB::raw('count(CASE WHEN inventory.quantity_available < 0 THEN 1 END)'),
            'wh_products_available'      => DB::raw('count(*)'),
            'wh_products_out_of_stock'   => DB::raw('count(CASE WHEN inventory.quantity_available = 0 THEN 1 END)'),
            'wh_products_required'       => DB::raw('count(CASE WHEN inventory.quantity_required > 0 THEN 1 END)'),
            'wh_products_stock_level_ok' => DB::raw('count(CASE WHEN inventory.quantity_required = 0 THEN 1 END)'),
        ];


        $this->casts = [
            'products_on_minus'           => 'float',
            'wh_products_available'       => 'float',
            'wh_products_out_of_stock'    => 'float',
            'wh_products_required'        => 'float',
            'wh_products_stock_level_ok'  => 'float',
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
