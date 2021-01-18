<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderKickController extends Controller
{
    /**
     * @param Request $request
     * @param $order_number
     */
    public function index(Request $request, $order_number)
    {
        $order = Order::query()->where(['order_number' => $order_number])->first();
        $order->touch();
        $this->respondOK200('Order Kicked');
    }
}
