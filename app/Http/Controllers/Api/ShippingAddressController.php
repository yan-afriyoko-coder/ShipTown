<?php

namespace App\Http\Controllers\Api;

use App\Models\OrderAddress;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShippingAddressRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 *
 */
class ShippingAddressController extends Controller
{
    /**
     * @param  StoreShippingAddressRequest $request
     * @return AnonymousResourceCollection
     */
    public function store(StoreShippingAddressRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $orderAddress = OrderAddress::find($validatedData['id']);

            if (!$orderAddress) {
                return response()->json(['error' => 'Shipping Address not found.'], 404);
            }

            $orderAddress->update($validatedData);
            return response()->json(['message' => 'Shipping Address updated successfully.', 'data' => $orderAddress], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unexpected error'], 503);
        }
    }
}
