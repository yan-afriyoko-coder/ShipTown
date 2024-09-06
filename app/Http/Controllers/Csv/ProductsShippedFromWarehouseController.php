<?php

namespace App\Http\Controllers\Csv;

use App\Http\Controllers\Controller;
use App\Models\OrderProduct;
use App\Traits\CsvFileResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Csv\CannotInsertRecord;

class ProductsShippedFromWarehouseController extends Controller
{
    use CsvFileResponse;

    /**
     * @throws CannotInsertRecord
     */
    public function index(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $query = OrderProduct::getSpatieQueryBuilder()
            ->select([
                'products.sku',
                'products.name',
                DB::raw('0 as qty_at_source'),
                DB::raw('0 as qty_at_destination'),
                'orders_products.quantity_shipped',
            ])
            ->join('products', 'products.id', '=', 'orders_products.product_id')
            ->where('orders_products.quantity_shipped', '>', 0);

        return $this->toCsvFileResponse($query->get(), 'warehouse_shipped.csv');
    }
}
