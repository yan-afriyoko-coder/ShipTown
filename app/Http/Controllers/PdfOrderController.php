<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;

/**
 * Class AddressLabel
 * @package App\Http\Controllers\Orders
 */
class PdfOrderController extends Controller
{
    /**
     * @param Request $request
     * @param string $order_number
     * @param string $template
     */
    public function show(Request $request, string $order_number, string $template)
    {
        $pdfString = OrderService::getOrderPdf($order_number, $template);

        $this->throwPdfResponse($pdfString);
    }
}
