<?php

namespace App\Modules\StocktakeSuggestions\src\Reports;

use App\Models\StocktakeSuggestion;
use App\Modules\Reports\src\Models\Report;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class StocktakeSuggestionsTotalsReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Stocktakes Suggestions';

        $this->baseQuery = StocktakeSuggestion::query()
            ->leftJoin('inventory', 'inventory_id', '=', 'inventory.id')
            ->rightJoin('warehouses', 'inventory.warehouse_id', '=', 'warehouses.id')
            ->groupBy('stocktake_suggestions.warehouse_id');

        $this->defaultSort = 'warehouse_code';

        $this->fields = [
            'warehouse_code'      => DB::raw('max(warehouses.code)'),
            'count'               => DB::raw('count(DISTINCT inventory.id)'),
        ];

        $this->casts = [
            'count'             => 'integer',
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
