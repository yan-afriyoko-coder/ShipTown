<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderPicklistResource;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderPicklistController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderProduct::getSpatieQueryBuilder()
            ->where('order_products.quantity_to_pick', '>', 0)
            ->select([
                'product_id',
                'name_ordered',
                'sku_ordered',
                DB::raw("sum(`quantity_to_pick`) as total_quantity_to_pick"),
                DB::raw("max(`inventory_source_quantity`) as inventory_source_quantity"),
                'inventory_source_shelf_location',
            ])
            ->groupBy([
                'name_ordered',
                'sku_ordered',
                'inventory_source_shelf_location',
                'product_id',
            ]);

        return OrderPicklistResource::collection($this->getPerPageAndPaginate($request, $query, 10));
    }
}
