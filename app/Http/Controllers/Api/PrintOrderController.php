<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\PdfOrderController;
use Illuminate\Http\Request;

class PrintOrderController extends PdfOrderController
{
    public function store(Request $request, $order_number, $template)
    {
        $pdf = parent::show($request, $order_number, $template);

        $response = $request->user()->newPdfPrintJob($template.'_'.$order_number.'_by_'. $request->user()->id, $pdf);

        return response(
            $response->getContent(),
            $response->getStatusCode()
        );
    }
}
