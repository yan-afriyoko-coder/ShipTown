<?php

namespace App\Modules\StocktakeSuggestions\src\Reports;

use App\Models\StocktakeSuggestion;
use App\Modules\Reports\src\Models\Report;
use Spatie\QueryBuilder\AllowedFilter;

class StocktakeSuggestionReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Stocktake Suggestions';

        $this->defaultSelect = implode(',', [
            'warehouse_code',
            'product_sku',
            'product_name',
            'reason',
            'points',
            'quantity_in_stock',
            'last_movement_at',
            'last_counted_at',
            'last_sold_at',
            'in_stock_since',
            'inventory_id',
            'product_id',
            'warehouse_id',
        ]);

        $this->defaultSort = 'points';

        $this->allowedIncludes = [
            'product',
            'product.tags',
            'product.prices',
            'inventory',
        ];

        if (request('title')) {
            $this->report_name = request('title').' ('.$this->report_name.')';
        }

        $this->baseQuery = StocktakeSuggestion::query()
            ->leftJoin('inventory', 'inventory.id', '=', 'stocktake_suggestions.inventory_id')
            ->leftJoin('products', 'products.id', '=', 'stocktake_suggestions.product_id');

        $this->fields = [
            'inventory_id'          => 'stocktake_suggestions.inventory_id',
            'product_id'            => 'stocktake_suggestions.product_id',
            'warehouse_id'          => 'stocktake_suggestions.warehouse_id',
            'reason'                => 'stocktake_suggestions.reason',
            'points'                => 'stocktake_suggestions.points',
            'warehouse_code'        => 'inventory.warehouse_code',
            'last_movement_at'      => 'inventory.last_movement_at',
            'last_counted_at'       => 'inventory.last_counted_at',
            'last_sold_at'          => 'inventory.last_sold_at',
            'in_stock_since'        => 'inventory.in_stock_since',
            'product_sku'           => 'products.sku',
            'product_name'          => 'products.name',
            'quantity_in_stock'     => 'inventory.quantity',
        ];

        $this->casts = [
            'inventory_id'                       => 'integer',
            'product_id'                         => 'integer',
            'warehouse_id'                       => 'integer',
            'warehouse_code'                     => 'string',
            'points'                             => 'float',
            'quantity_in_stock'                  => 'float',
            'product_sku'                        => 'string',
            'product_name'                       => 'string',
        ];

        $this->addFilter(
            AllowedFilter::callback('product_has_tags', function ($query, $value) {
                $query->whereHas('product', function ($query) use ($value) {
                    $query->hasTags($value);
                });
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
