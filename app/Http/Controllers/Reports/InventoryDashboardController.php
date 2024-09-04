<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Modules\Reports\src\Models\InventoryDashboardReport;
use Illuminate\Http\Request;

class InventoryDashboardController extends Controller
{
    public function index(Request $request)
    {
        $report = new InventoryDashboardReport;

        return $report->response($request);
    }
}
