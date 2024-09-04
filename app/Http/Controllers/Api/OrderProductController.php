<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderProducts\UpdateRequest;
use App\Http\Resources\OrderProductResource;
use App\Models\OrderProduct;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class OrderProductController.
 *
 * @group Order
 */
class OrderProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $query = OrderProduct::getSpatieQueryBuilder();

        return OrderProductResource::collection($this->getPaginatedResult($query));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, OrderProduct $product): OrderProductResource
    {
        $product->update($request->validated());

        return OrderProductResource::make($product);
    }
}
