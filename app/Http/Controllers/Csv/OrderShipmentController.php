<?php

namespace App\Http\Controllers\Csv;

use App\Http\Controllers\Controller;
use App\Models\OrderShipment;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;

class OrderShipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderShipment::select([
            'orders.order_number',
            'order_shipments.shipping_number'
        ])
            ->join('orders', 'orders.id', '=', 'order_shipments.order_id')
            ->whereDate('order_shipments.created_at', '=', Carbon::today());

        return self::csvFile($query->get(), 'order_shipments.csv');
    }

    /**
     * @param Collection $recordSet
     * @param string $filename
     * @return Application|ResponseFactory|Response
     * @throws CannotInsertRecord
     */
    private static function csvFile(Collection $recordSet, string $filename)
    {
        $csv = Writer::createFromFileObject(new \SplTempFileObject);

        if ($recordSet->isNotEmpty()) {
            $csv->insertOne(array_keys($recordSet[0]->getAttributes()));

            foreach ($recordSet as $record) {
                $csv->insertOne($record->toArray());
            }
        }

        return response((string)$csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
