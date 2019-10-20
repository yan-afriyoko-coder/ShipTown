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
}
