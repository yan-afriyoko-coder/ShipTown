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
            ->select([
                'name_ordered',
                'sku_ordered',
                'inventory_source_shelf_location',
                'product_id',
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
