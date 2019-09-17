<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function store(Request $request){

        $snsTopic = new SnsTopicController('products');

        $message = $request->getContent();

        if ($snsTopic->publish_message($message)) {
            return response()->json("ok", 200);
        };

    }
}
