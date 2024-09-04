<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderAddressRequest;
use App\Http\Resources\OrderAddressResource;
use App\Models\OrderAddress;

class OrderAddressController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     */
    public function update(UpdateOrderAddressRequest $request, OrderAddress $address): OrderAddressResource
    {
        $address->update($request->validated());

        return OrderAddressResource::make($address);
    }
}
