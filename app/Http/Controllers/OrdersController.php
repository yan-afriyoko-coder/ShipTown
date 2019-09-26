<?php

namespace App\Http\Controllers;

use App\Events\EventTypes;
use Illuminate\Http\Request;

class OrdersController extends Controller
{

    public function store(Request $request){

        $order = json_decode($request->getContent(), true);

        event(EventTypes::ORDER_CREATED, new EventTypes($order));

    }
}
