<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserConfigurationController extends Controller
{
    public function show()
    {
        $jsonConfig = json_encode("{}");

        return response($jsonConfig, 200);
    }

    public function store(Request $request)
    {
        $data = [];

        return response($data, 200);
    }
}
