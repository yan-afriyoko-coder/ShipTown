<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderProducts\UpdateRequest;
use App\Http\Resources\OrderProductResource;
use App\Models\OrderProduct;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class OrderProductController
 * @package App\Http\Controllers\Api\Order
 *
 * @group Order
 */
class OrderProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $query = OrderProduct::getSpatieQueryBuilder();

        return OrderProductResource::collection($this->getPaginatedResult($query));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param OrderProduct $product
     * @return OrderProductResource
     */
    public function update(UpdateRequest $request, OrderProduct $product): OrderProductResource
    {
        $this->setStatusCode(500)->throwJsonResponse('');
        $product->update($request->validated());

        return new OrderProductResource($product);
    }
}
