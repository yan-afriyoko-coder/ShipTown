<?php

namespace App\Http\Controllers\Api\Picklist;

use App\Http\Controllers\Controller;
use App\Http\Requests\PickDestroyRequest;
use App\Http\Requests\Picklist\StoreDeletedPickRequest;
use App\Models\OrderProduct;
use App\Models\OrderProductPick;
use App\Models\Pick;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PicklistPickController.
 */
class PicklistPickController extends Controller
{
    /**
     * @param StoreDeletedPickRequest $request
     *
     * @return JsonResource
     */
    public function store(StoreDeletedPickRequest $request): JsonResource
    {
        if ($request->get('quantity_picked', 0) !== 0) {
            $key = 'quantity_picked';
            $quantityToDistribute = $request->get('quantity_picked');
        } else {
            $key = 'quantity_skipped_picking';
            $quantityToDistribute = $request->get('quantity_skipped_picking');
        }

        $orderProducts = OrderProduct::query()
            ->whereIn('id', $request->get('order_product_ids'))
            ->oldest('order_id')
            ->get();

        $first = $orderProducts->first();

        /** @var Pick $pick */
        $pick = Pick::query()->create([
            'user_id'                  => request()->user()->getKey(),
            'warehouse_code'           => request()->user()->warehouse->code,
            'product_id'               => $first['product_id'],
            'sku_ordered'              => $first['sku_ordered'],
            'name_ordered'             => $first['name_ordered'],
            'quantity_picked'          => $request->get('quantity_picked', 0),
            'quantity_skipped_picking' => $request->get('quantity_skipped_picking', 0),
        ]);

        foreach ($orderProducts as $orderProduct) {
            $quantity = min($quantityToDistribute, $orderProduct->quantity_to_pick);
            $orderProduct->fill([
                $key => $orderProduct->getAttribute($key) + $quantity,
            ]);
            $orderProduct->save();

            OrderProductPick::query()->create([
                'pick_id'                 => $pick->id,
                'order_product_id'        => $orderProduct->id,
                $key                      => $quantity,
            ]);

            $quantityToDistribute -= $quantity;
            if ($quantityToDistribute <= 0) {
                break;
            }
        }

        return JsonResource::make([$pick]);
    }

    public function destroy(PickDestroyRequest $request, Pick $pick): JsonResource
    {
        $pick->delete();

        return JsonResource::make([$pick]);
    }
}
