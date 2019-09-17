<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductsController extends SnsBaseController
{
    protected $topicNamePrefix = "Products";

    function store(Request $request){

        $message = $request->getContent();

        if ($this->publishMessage($message)) {

            return response()->json("ok", 200);

        };

    }
}
