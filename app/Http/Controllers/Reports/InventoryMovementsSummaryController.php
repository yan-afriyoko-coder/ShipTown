<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Modules\Reports\src\Models\InventoryMovementsSummaryReport;
use App\Modules\Reports\src\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryMovementsSummaryController extends Controller
{
    public function index(Request $request)
    {
        $report = new InventoryMovementsSummaryReport();
        return $report->response($request);
    }
}
