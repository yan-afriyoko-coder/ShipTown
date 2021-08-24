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
        $orderProductShipment->user()->associate($request->user());
        $orderProductShipment->save();

        return new OrderProductShipmentResource($orderProductShipment);
    }
}
