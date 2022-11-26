<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\StocktakeSuggestions\src\Reports\StoctakeSuggestionReport;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class StocktakeSuggestionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $report = new StoctakeSuggestionReport();

        $query = $report->queryBuilder()
            ->groupBy(['stocktake_suggestions.inventory_id', 'stocktake_suggestions.product_id', 'stocktake_suggestions.warehouse_id'])
            ->simplePaginate(request()->get('per_page', 10));

        return JsonResource::collection($query);
    }
}
