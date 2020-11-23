<?php

namespace App\Http\Controllers\Api\Picklist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Picklist\StoreDeletedPickRequest;
use App\Models\OrderProduct;
use App\Models\Pick;
use Illuminate\Http\Resources\Json\JsonResource;

class PicklistPickController extends Controller
{
    public function store(StoreDeletedPickRequest $request)
    {
        if ($request->get('quantity_picked', 0) !== 0) {
            $key = 'quantity_picked';
            $quantityToDistribute = $request->get('quantity_picked');
        } else {
            $key = 'quantity_skipped_picking';
            $quantityToDistribute = $request->get('quantity_skipped_picking');
        }

        $orderProducts = OrderProduct::whereIn('id', $request->get('order_product_ids'))
            ->latest('order_id')
            ->get();

        foreach ($orderProducts as $orderProduct) {
            $quantity = min($quantityToDistribute, $orderProduct->quantity_to_pick);
            $orderProduct->update([
                $key => $orderProduct->getAttribute($key) + $quantity
            ]);
            $quantityToDistribute -= $quantity;
            if ($quantityToDistribute <= 0) {
                break;
            }
        }

        $first = $orderProduct->first();

        Pick::create([
            'user_id' => request()->user()->getKey(),
            'product_id' => $first['product_id'],
            'sku_ordered' => $first['sku_ordered'],
            'name_ordered' => $first['name_ordered'],
            'quantity_picked' =>  $request->get('quantity_picked', 0),
            'quantity_skipped_picking' =>  $request->get('quantity_skipped_picking', 0),
            'quantity_required' =>  0,
        ]);

        return JsonResource::collection($orderProducts);
    }
}
