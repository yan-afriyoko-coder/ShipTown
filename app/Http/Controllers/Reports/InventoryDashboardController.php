<?php

namespace App\Http\Controllers\Reports;

use App\Exceptions\InvalidSelectException;
use App\Http\Controllers\Controller;
use App\Modules\Reports\src\Models\InventoryDashboardReport;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class InventoryDashboardController extends Controller
{
    /**
     * @throws InvalidSelectException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(Request $request)
    {
        $report = new InventoryDashboardReport();

        return $report->response($request);
    }
}
