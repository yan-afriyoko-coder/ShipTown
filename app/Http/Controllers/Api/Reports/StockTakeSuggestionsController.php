<?php

namespace App\Http\Controllers\Api\Reports;

use App\Modules\StocktakeSuggestions\src\Reports\StoctakeSuggestionReport;
use Illuminate\Http\Resources\Json\JsonResource;

class StockTakeSuggestionsController
{
    public function index()
    {
        return StoctakeSuggestionReport::toJsonResource();
    }
}
