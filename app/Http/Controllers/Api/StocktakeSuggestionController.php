<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StocktakeSuggestion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class StocktakeSuggestionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = QueryBuilder::for(StocktakeSuggestion::class)
            ->allowedFilters([
                'sku',
                'name',
                'quantity',
                'quantity_to_stocktake',
                AllowedFilter::exact('warehouse.id'),
                AllowedFilter::exact('warehouse.code')
            ])
            ->allowedSorts([
                'points',
                'inventory.warehouse_id',
                'inventory.shelf_location',
                'inventory.quantity'])
            ->allowedIncludes([
                'product',
                'warehouse'])
            ->defaultSort('points');

        return JsonResource::collection($this->getPaginatedResult($query));
    }
}
