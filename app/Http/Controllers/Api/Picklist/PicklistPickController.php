<?php

namespace App\Http\Controllers\Api\Picklist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Picklist\StoreDeletedPickRequest;
use App\Models\OrderProduct;
use App\Models\Pick;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PicklistPickController.
 */
class PicklistPickController extends Controller
{
    /**
     * @param StoreDeletedPickRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function store(StoreDeletedPickRequest $request): AnonymousResourceCollection
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
            $orderProduct->fill([
                $key => $orderProduct->getAttribute($key) + $quantity,
            ]);
            $orderProduct->save();
            $quantityToDistribute -= $quantity;
            if ($quantityToDistribute <= 0) {
                break;
            }
        }

        $first = $orderProducts->first();

        $pick = Pick::create([
            'user_id'                  => request()->user()->getKey(),
            'warehouse_code'           => request()->user()->warehouse->code,
            'product_id'               => $first['product_id'],
            'sku_ordered'              => $first['sku_ordered'],
            'name_ordered'             => $first['name_ordered'],
            'quantity_picked'          => $request->get('quantity_picked', 0),
            'quantity_skipped_picking' => $request->get('quantity_skipped_picking', 0),
            'quantity_required'        => 0,
        ]);

        return JsonResource::collection($orderProducts);
    }
}
