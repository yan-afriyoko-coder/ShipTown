<?php

namespace App\Http\Controllers;

use App\Events\EventTypes;
use App\Http\Requests\DeleteOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrdersController extends Controller
{

    public function index() {
        return Order::all();
    }

    public function store(StoreOrderRequest $request)
    {
        $order = Order::updateOrCreate(
            ['order_number' => $request->order_number],
            ['order_json' => $request->all()]);

        event(EventTypes::ORDER_CREATED, new EventTypes($order));

        return response()->json($order, 200);
    }

    public function destroy(DeleteOrderRequest $request)
    {
       $order = Order::where('order_number', $request->order_number)->firstOrFail();

       $order->delete();

       event(EventTypes::ORDER_DELETED, new EventTypes($order));

       return $this->respond_OK_200();
    }

}
