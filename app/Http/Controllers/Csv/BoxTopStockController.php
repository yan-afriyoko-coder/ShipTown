<?php

namespace App\Http\Controllers\Csv;

use App\Http\Controllers\Controller;
use App\Models\OrderShipment;
use App\Modules\BoxTop\src\Models\WarehouseStock;
use App\Modules\BoxTop\src\Services\BoxTopService;
use App\Traits\CsvFileResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Csv\CannotInsertRecord;

class BoxTopStockController extends Controller
{
    use CsvFileResponse;

    /**
     * @throws CannotInsertRecord
     */
    public function index(Request $request)
    {
        $response = BoxTopService::apiClient()->getStockCheckByWarehouse();

        $vars = collect($response->toArray());

        $vars = $vars->map(function ($record) {
            $record['Attributes'] = json_encode($record['Attributes']);
            return $record;
        });

        WarehouseStock::query()->delete();
        WarehouseStock::query()->insert($vars->toArray());

        $query = WarehouseStock::query()->select([
            'SKUGroup',
            'SKUNumber',
            'SKUName',
            'Warehouse',
            'WarehouseQuantity',
            'Allocated',
            'Available',
        ]);

        return $this->toCsvFileResponse($query->get(), 'boxtop_stock.csv');
    }
}
