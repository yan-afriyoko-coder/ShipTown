<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\OrderShipment;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $shipments = OrderShipment::getSpatieQueryBuilder()
            ->with('user')
            ->with('order')
            ->limit(1000)
            ->get()
            ->toArray();

        return view('reports.shipments', ['shipments' => $shipments]);
    }
}
