<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImportOrdersController extends Controller
{
    public function fromApi2Cart()
    {
        $responseText = [
            "message" => "",
            "error_id" => 0,
        ];

        return response()->json($responseText,200);
    }
}
