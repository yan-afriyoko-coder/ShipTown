<?php

namespace App\Http\Controllers;

use App\Events\EventTypes;
use App\Http\Requests\DeleteOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

        return response()->json($order, 200);
    }

    public function destroy($order_number)
    {
        try {
            $order = Order::where('order_number', $order_number)->firstOrFail();
        }
        catch (ModelNotFoundException $e)
        {
            return $this->respond_NotFound();
        }

       $order->delete();

       event(EventTypes::ORDER_DELETED, new EventTypes($order));

       return $this->respond_OK_200();
    }

}
