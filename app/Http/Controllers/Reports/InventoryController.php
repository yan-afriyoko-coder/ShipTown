<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Inventory;
use App\Traits\CsvFileResponse;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use League\Csv\CannotInsertRecord;

class InventoryController extends Controller
{
    use CsvFileResponse;

    /**
     * @param Request $request
     *
     * @return Application|Factory|View
     * @throws CannotInsertRecord
     */
    public function index(Request $request)
    {
        $query = Inventory::getSpatieQueryBuilder();

        if ($request->has('filename')) {
            return $this->toCsvFileResponse($query->get(), $request->get('filename'));
        }

        return view('reports.inventory', [
            'data' => OrderResource::collection($this->getPaginatedResult($query))
        ]);
    }
}
