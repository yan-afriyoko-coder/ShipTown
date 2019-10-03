<?php

namespace App\Http\Controllers;

use App\Events\EventTypes;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class OrdersController extends Controller
{

    public function store(StoreOrderRequest $request){

        $order = Order::updateOrCreate(
            ['order_number' => $request->order_number],
            ['order_json' => $request->all()]);

        event(EventTypes::ORDER_CREATED, new EventTypes($order));

        return response()->json($order, 200);

    }

    public function destroy($order_number) {
       $order = Order::where('order_number', $order_number)->firstOrFail();
       $order->delete();
    }

}
