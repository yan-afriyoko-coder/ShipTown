<?php

namespace App\Http\Controllers;

use App\Events\EventTypes;
use App\Http\Requests\DeleteOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Managers\Config;
use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrdersController extends Controller
{

    public function index() {
        return Order::all();
    }

    public function store(StoreOrderRequest $request)
    {
        $order = Order::query()->updateOrCreate(
            ['order_number' => $request->order_number],
            $request->all()
        );

        return response()->json($order, 200);
    }

    public function destroy($order_number)
    {
        try {
            $order = Order::query()->where('order_number', $order_number)->firstOrFail();
        }
        catch (ModelNotFoundException $e)
        {
            return $this->respond_NotFound();
        }

       $order->delete();

       return $this->respond_OK_200();
    }
}
