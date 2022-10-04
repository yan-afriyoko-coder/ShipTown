<?php

namespace App\Http\Controllers\Reports;

use App\Exceptions\InvalidSelectException;
use App\Http\Controllers\Controller;
use App\Modules\StocktakeSuggestions\src\Reports\StocktakeSuggestionsTotalsReport;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class StocktakeSuggestionsReportController extends Controller
{
    /**
     * @throws InvalidSelectException
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function index(Request $request)
    {
        $report = new StocktakeSuggestionsTotalsReport();

        return $report->response($request);
    }
}
