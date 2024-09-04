<?php

namespace App\Modules\StocktakeSuggestions\src\Reports;

use App\Models\Warehouse;
use App\Modules\Reports\src\Models\Report;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class StocktakeSuggestionsTotalsReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Stocktakes Suggestions';

        $this->baseQuery = Warehouse::query();

        $this->defaultSort = 'warehouse_code';

        $this->fields = [
            'warehouse_code' => DB::raw('warehouses.code'),
            'count' => DB::raw('SELECT count(DISTINCT inventory_id) FROM stocktake_suggestions WHERE stocktake_suggestions.warehouse_id = warehouses.id'),
        ];

        $this->casts = [
            'count' => 'integer',
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
