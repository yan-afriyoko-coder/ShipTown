<?php

namespace App\Http\Controllers\Csv;

use App\Http\Controllers\Controller;
use App\Models\OrderShipment;
use App\Traits\CsvFileResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderShipmentController extends Controller
{
    use CsvFileResponse;

    public function index(Request $request)
    {
        $query = OrderShipment::select([
            'orders.order_number',
            'order_shipments.shipping_number'
        ])
            ->join('orders', 'orders.id', '=', 'order_shipments.order_id')
            ->whereDate('order_shipments.created_at', '=', Carbon::today());

        return $this->toCsvFileResponse($query->get(), 'order_shipments.csv');
    }
}
