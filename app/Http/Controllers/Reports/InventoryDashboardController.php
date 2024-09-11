<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Modules\Reports\src\Models\InventoryDashboardRecords;
use Illuminate\Http\Request;

class InventoryDashboardController extends Controller
{
    public function index(Request $request)
    {
        $reports = InventoryDashboardRecords::all();

        return view('reports.inventory-dashboard', compact('reports'));
    }
}
