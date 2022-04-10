<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Inventory;
use App\Models\OrderShipment;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $query = Inventory::getSpatieQueryBuilder();

        $data = OrderResource::collection($this->getPaginatedResult($query));

        return view('reports.inventory', ['data' => $data]);
    }
}
