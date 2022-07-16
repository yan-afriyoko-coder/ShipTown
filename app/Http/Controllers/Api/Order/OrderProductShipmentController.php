<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderProductShipment\StoreRequest;
use App\Http\Resources\OrderProductShipmentResource;
use App\Models\OrderProductShipment;

/**
 * Class OrderProductController.
 *
 * @group Order
 */
class OrderProductShipmentController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param StoreRequest $request
     *
     * @return OrderProductShipmentResource
     */
    public function store(StoreRequest $request): OrderProductShipmentResource
    {
        $orderProductShipment = new OrderProductShipment($request->validated());
        $orderProductShipment->user_id = $request->user()->getKey();
        $orderProductShipment->warehouse_id = $orderProductShipment->user->warehouse_id;
        $orderProductShipment->save();

        $orderProduct = $orderProductShipment->orderProduct;

        $orderProduct->update([
            'quantity_shipped' => $orderProduct->quantity_shipped + $request->get('quantity_shipped', 0)
        ]);

        return new OrderProductShipmentResource($orderProductShipment);
    }
}
