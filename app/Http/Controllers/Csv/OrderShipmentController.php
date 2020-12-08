<?php

namespace App\Http\Controllers\Csv;

use App\Http\Controllers\Controller;
use App\Models\OrderShipment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderShipmentController extends Controller
{
    public function index(Request $request)
    {
        $recordSet = OrderShipment::select([
            'orders.order_number',
            'order_shipments.shipping_number'
        ])
            ->join('orders', 'orders.id', '=', 'order_shipments.order_id')
            ->whereDate('order_shipments.created_at', '=', Carbon::today())
            ->get();

        $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject);

        if ($recordSet->isNotEmpty()) {
            $csv->insertOne(array_keys($recordSet[0]->getAttributes()));

            foreach ($recordSet as $record) {
                $csv->insertOne($record->toArray());
            }
        }

        return response((string) $csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="people.csv"',
        ]);
    }
}
