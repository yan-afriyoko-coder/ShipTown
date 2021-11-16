<?php

namespace App\Http\Controllers\Csv;

use App\Http\Controllers\Controller;
use App\Models\OrderShipment;
use App\Modules\BoxTop\src\Models\WarehouseStock;
use App\Modules\BoxTop\src\Services\BoxTopService;
use App\Traits\CsvFileResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Csv\CannotInsertRecord;

class BoxTopStockController extends Controller
{
    use CsvFileResponse;

    /**
     * @throws CannotInsertRecord
     */
    public function index(Request $request)
    {
        $query = DB::select("
SELECT
 modules_boxtop_warehouse_stock.SKUNumber AS SKU_BoxTop,
 products.sku as SKU_RMS,
 modules_boxtop_warehouse_stock.SKUName,
 modules_boxtop_warehouse_stock.Available

FROM `modules_boxtop_warehouse_stock`

LEFT JOIN products_aliases
  ON modules_boxtop_warehouse_stock.SKUNumber = products_aliases.alias

LEFT JOIN products
  ON products_aliases.product_id = products.id

ORDER BY SKU_RMS, SKUName ASC
");

//        dd($query);
        $vars = collect($query)->map(function ($record) {
            dd($record);
            $record['Attributes'] = json_encode($record['Attributes']);
            return $record;
        });

        dd($vars->toArray());

//        WarehouseStock::query()->delete();
//        WarehouseStock::query()->insert($vars->toArray());
//
//        $query = WarehouseStock::query()->select([
//            'SKUGroup',
//            'SKUNumber',
//            'SKUName',
//            'Warehouse',
//            'WarehouseQuantity',
//            'Allocated',
//            'Available',
//        ]);



        return $this->toCsvFileResponse(collect($vars), 'boxtop_stock.csv');
    }
}
