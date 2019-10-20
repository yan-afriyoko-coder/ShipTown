<?php

namespace App\Http\Controllers;

use App\Jobs\JobImportOrderApi2Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImportOrdersController extends Controller
{
    public function fromApi2Cart()
    {
        JobImportOrderApi2Cart::dispatch();

        Log::info('Import Order from api2cart dispatched');

        $responseText = [
            "message" => "",
            "error_id" => 0,
        ];

        return response()->json($responseText,200);
    }
}
