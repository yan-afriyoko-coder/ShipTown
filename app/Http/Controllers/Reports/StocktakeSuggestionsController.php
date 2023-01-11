<?php

namespace App\Http\Controllers\Reports;

use App\Modules\StocktakeSuggestions\src\Reports\StoctakeSuggestionReport;
use Illuminate\Http\Request;

class StocktakeSuggestionsController
{
    public function index(Request $request): mixed
    {
        $report = new StoctakeSuggestionReport();

        return $report->response($request);
    }
}
