<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StocktakeSuggestion;
use App\Modules\Reports\src\Models\Report;
use App\Modules\StocktakeSuggestions\src\Reports\StoctakeSuggestionReport;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class StocktakeSuggestionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $report = new StoctakeSuggestionReport();
        $query = $report->queryBuilder()
//            ->selectRaw('inventory_id, sum(points) as points')
            ->groupBy('inventory_id');
//
//        $report = new Report();
//
//        $report->baseQuery = QueryBuilder::for(StocktakeSuggestion::class)
//            ->allowedFilters([
//                'sku',
//                'name',
//                'quantity',
//                'quantity_to_stocktake',
//                AllowedFilter::exact('warehouse.id'),
//                AllowedFilter::exact('warehouse.code')
//            ])
//            ->allowedSorts([
//                'points',
//                'inventory.warehouse_id',
//                'inventory.shelf_location',
//                'inventory.quantity'])
//            ->allowedIncludes([
//                'product',
//                'warehouse'])
//            ->defaultSort('points');
//
//        $report->baseQuery = StocktakeSuggestion::query();
//
//        $report->defaultSelect = implode(',', [
//            'id',
//            'warehouse_code',
//            'product_sku',
//            'product_name',
//            'quantity_required',
//            'quantity_available',
//            'quantity_incoming',
//            'reorder_point',
//            'restock_level',
//            'warehouse_quantity'
//        ]);
//
//        $report->defaultSort = '-quantity_required';
//
//        $report->allowedIncludes = ['tags'];

        return JsonResource::collection($this->getPaginatedResult($query));
    }
}
