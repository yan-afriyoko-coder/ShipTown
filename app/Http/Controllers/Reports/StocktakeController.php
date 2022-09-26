<?php

namespace App\Http\Controllers\Reports;

use App\Exceptions\InvalidSelectException;
use App\Http\Controllers\Controller;
use App\Modules\Reports\src\Models\RestockingReport;
use App\Modules\Reports\src\Models\StocktakesReport;
use App\Traits\CsvFileResponse;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class StocktakeController extends Controller
{
    use CsvFileResponse;

    /**
     * @param Request $request
     *
     * @return Application|Factory|View
     * @throws InvalidSelectException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(Request $request)
    {
        $report = new StocktakesReport();
        $report->setPerPage($request->get('per_page', 100));

        return $report->response($request);
    }
}
