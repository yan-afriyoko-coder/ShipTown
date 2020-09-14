<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\PdfOrderController;
use Illuminate\Http\Request;

class PrintOrderController extends PdfOrderController
{
    public function store(Request $request, $order_number, $template)
    {
        $pdf = parent::show($request, $order_number, $template);

        $job_name = $template . '_' . $order_number . '_by_' . $request->user()->id;

        $response = $request->user()->newPdfPrintJob($job_name, $pdf);

        return response(
            $response->getContent(),
            $response->getStatusCode()
        );
    }
}
