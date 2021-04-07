<?php

namespace App\Http\Controllers\Csv;

use App\Http\Controllers\Controller;
use App\Models\OrderShipment;
use Illuminate\Http\Request;

class ReadyOrderShipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderShipment::getSpatieQueryBuilder();

        $this->throwCsvDownloadResponse($query);
    }
}
