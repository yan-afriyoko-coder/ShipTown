<?php

namespace App\Http\Controllers\Csv;

use App\Http\Controllers\Controller;
use App\Models\OrderProduct;
use App\Traits\CsvFileResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductsShippedFromWarehouseController extends Controller
{
    use CsvFileResponse;

    public function index(Request $request)
    {
        $query = OrderProduct::getSpatieQueryBuilder()
            ->select([
                'products.sku',
                'products.name',
                DB::raw('0 as qty_at_source'),
                DB::raw('0 as qty_at_destination'),
                'order_products.quantity_shipped',
            ])
            ->join('products', 'products.id', '=', 'order_products.product_id')
            ->join('orders', 'orders.id', '=', 'order_products.order_id')
            ->where('order_products.quantity_shipped', '>', 0);

        return $this->toCsvFileResponse($query->get(), 'warehouse_shipped.csv');
    }
}
