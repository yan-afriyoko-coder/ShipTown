<?php

namespace App\Http\Controllers\Api;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderCheckRequest\StoreRequest;
use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderCheckRequestController extends Controller
{
    public function store(StoreRequest $request)
    {
        ActiveOrderCheckEvent::dispatch(
            Order::findOrFail($request->get('order_id'))
        );

        return JsonResource::make($request->validated());
    }
}
