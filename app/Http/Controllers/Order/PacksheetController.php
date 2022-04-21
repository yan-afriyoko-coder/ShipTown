<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PacksheetController extends Controller
{
    public function show(Request $request, $order_number)
    {
        return view('order-packsheet', ['order_number' => $order_number]);
    }
}
