<?php

namespace App\Modules\StocktakeSuggestions\src\Reports;

use App\Models\StocktakeSuggestion;
use App\Modules\Reports\src\Models\Report;
use Spatie\QueryBuilder\AllowedFilter;

class StoctakeSuggestionsDetailedReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->view = 'reports.inventory-report';

        $this->report_name = 'Stocktake Suggestions Detailed';

        $this->defaultSelect = implode(',', [
            'inventory_id',
            'points',
            'reason',
        ]);

        $this->defaultSort = 'points';

        $this->allowedIncludes = [
            'product',
            'product.tags',
            'inventory',
        ];

        if (request('title')) {
            $this->report_name = request('title').' ('.$this->report_name.')';
        }

        $this->baseQuery = StocktakeSuggestion::query()
            ->leftJoin('inventory', 'inventory.id', '=', 'stocktake_suggestions.inventory_id');

        $this->fields = [
            'inventory_id'                       => 'inventory_id',
            'warehouse_id'                       => 'inventory.warehouse_id',
            'warehouse_code'                     => 'inventory.warehouse_code',
            'points'                             => 'points',
            'reason'                             => 'reason',
        ];

        $this->casts = [
            'warehouse_code'     => 'string',
            'reason'             => 'string',
            'points'             => 'float',
        ];

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
