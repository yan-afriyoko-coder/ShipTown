<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\PdfOrderController;
use App\Modules\PrintNode\src\Client;
use App\Modules\PrintNode\src\Models\PrintJob;
use App\Modules\PrintNode\src\PrintNode;
use App\Modules\PrintNode\src\Resources\PrintJobResource;
use Illuminate\Http\Request;

/**
 * Class PrintOrderController
 * @package App\Http\Controllers\Api
 */
class PrintOrderController extends PdfOrderController
{
    public function store(Request $request, $order_number, $template)
    {
        $pdf = parent::show($request, $order_number, $template);

        $printJob = new PrintJob();
        $printJob->printer_id = $request->user()->printer_id;
        $printJob->title = $template . '_' . $order_number . '_by_' . $request->user()->id;
        $printJob->pdf = base64_encode($pdf);

        PrintNode::print($printJob);

        return PrintJobResource::make($printJob);
    }
}
