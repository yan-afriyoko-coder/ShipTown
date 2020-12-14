<?php

namespace App\Http\Controllers\Csv;

use App\Http\Controllers\Controller;
use App\Models\OrderShipment;
use App\Traits\CsvFileResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PartialOrderShipmentController extends Controller
{
    use CsvFileResponse;

    public function index(Request $request)
    {
        $query = OrderShipment::select([
                'orders.status_code',
                'orders.order_number',
                'order_shipments.shipping_number'
            ])
            ->join('orders', 'orders.id', '=', 'order_shipments.order_id')
            ->whereDate('order_shipments.created_at', '=', Carbon::today())
            ->where('orders.status_code', '<>', 'ready');

        return $this->toCsvFileResponse($query->get(), 'partial_order_shipments.csv');
    }
}
