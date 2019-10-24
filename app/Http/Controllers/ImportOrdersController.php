<?php

namespace App\Http\Controllers;

use App\Jobs\JobImportOrderApi2Cart;
use App\Managers\UserConfigurationManager;
use App\Models\Product;
use App\Models\UserConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;


class ImportOrdersController extends Controller
{
    public function fromApi2Cart()
    {
        $user_id = auth()->id();

        $api2cart_store_key = UserConfigurationManager::getValue("api2cart_store_key", $user_id);

        JobImportOrderApi2Cart::dispatch(auth()->user(), $api2cart_store_key);

        info('Import Order from api2cart dispatched');

        $responseText = [
            "message" => "",
            "error_id" => 0,
        ];

        return response()->json($responseText,200);
    }
}
