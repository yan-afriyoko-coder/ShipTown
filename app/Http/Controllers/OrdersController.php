<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrdersController extends SnsBaseController
{
    protected $topicNamePrefix = "Orders";

    public function store(Request $request){

        $message = $request->getContent();

        if ($this->publishMessage($message)) {
            return response()->json("ok", 200);
        };

    }
}
