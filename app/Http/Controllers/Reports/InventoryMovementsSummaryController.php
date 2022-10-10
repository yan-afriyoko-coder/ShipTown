<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Modules\Reports\src\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryMovementsSummaryController extends Controller
{
    public function index(Request $request)
    {
        $report = new Report();
        $report->report_name = 'Inventory Movements Summary';

        $report->fields = [
            'description' => DB::raw('IFNULL(inventory_movements.description, "")'),
            'warehouse_code' => DB::raw('IFNULL(inventory.warehouse_code, "")'),
            'count' => DB::raw('count(*)'),
        ];

        $report->baseQuery = InventoryMovement::query()
            ->leftJoin('inventory', 'inventory.id', '=', 'inventory_movements.inventory_id')
            ->groupByRaw('inventory_movements.description, inventory.warehouse_code')
            ->orderByRaw('inventory_movements.description, inventory.warehouse_code');

        return $report->response($request);
    }
}
