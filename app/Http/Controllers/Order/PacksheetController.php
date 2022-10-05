<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PacksheetController extends Controller
{
    public function show(Request $request, $order_id)
    {
        return view('packsheet', ['order_id' => $order_id]);
    }
}
