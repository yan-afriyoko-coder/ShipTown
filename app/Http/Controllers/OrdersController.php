<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function store(Request $request){

        $snsTopic = new SnsTopicController('orders');

        $message = $request->getContent();

        if ($snsTopic->publish_message($message)) {
            return response()->json("ok", 200);
        };

    }
}
