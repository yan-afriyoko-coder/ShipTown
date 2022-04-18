<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Inventory;
use App\Modules\Reports\src\Http\Resources\ReportResource;
use App\Modules\Reports\src\Models\Report;
use App\Modules\Reports\src\Models\RestockingReport;
use App\Traits\CsvFileResponse;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use League\Csv\CannotInsertRecord;

class RestockingReportController extends Controller
{
    use CsvFileResponse;

    /**
     * @param Request $request
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $report = new RestockingReport();

        return $report->response($request);
    }
}
