<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderPicklist\StoreRequest;
use App\Http\Resources\OrderPicklistResource;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class PicklistController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderProduct::getSpatieQueryBuilder()
            ->where('quantity_to_pick', '>', 0)
            ->select([
                'product_id',
                'name_ordered',
                'sku_ordered',
                'inventory_source_shelf_location',
                DB::raw("sum(`quantity_to_pick`) as total_quantity_to_pick"),
                DB::raw("max(`inventory_source_quantity`) as inventory_source_quantity"),
                DB::raw("GROUP_CONCAT(id ORDER BY id SEPARATOR ',' ) AS order_product_ids"),
            ])
            ->groupBy([
                'order_products.name_ordered',
                'order_products.sku_ordered',
                'order_products.product_id',
                'inventory_source_shelf_location',
            ]);

        return OrderPicklistResource::collection($this->getPerPageAndPaginate($request, $query, 10));
    }

    public function store(StoreRequest $request)
    {
        $totalQuantityPicked = $request->get('quantity_picked');

        $orderProducts = OrderProduct::whereIn('id', $request->get('order_product_ids'))
            ->orderBy('order_id')
            ->get();

        foreach ($orderProducts as $orderProduct) {
            $quantity = $totalQuantityPicked < $orderProduct->quantity_to_pick
                ? $totalQuantityPicked
                : $orderProduct->quantity_to_pick;

            $orderProduct->update([
                'quantity_picked' => $orderProduct->quantity_picked + $quantity,
            ]);

            $totalQuantityPicked = $totalQuantityPicked - $quantity;

            if ($totalQuantityPicked <= 0) {
                continue;
            }
        }

        return JsonResource::collection($orderProducts);
    }
}
