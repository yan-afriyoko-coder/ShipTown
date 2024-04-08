<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Modules\Reports\src\Models\InventoryTransferReport;
use App\Traits\CsvFileResponse;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryTransfersController extends Controller
{
    use CsvFileResponse;
    /**
     * @param Request $request
     *
     * @return Application|Factory|View
     */

    public function index(Request $request)
    {
        $report = new InventoryTransferReport();
        return $report->response($request);
    }
}
