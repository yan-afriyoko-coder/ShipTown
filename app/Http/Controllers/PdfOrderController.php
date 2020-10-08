<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
     * @return ResponseFactory|Response
     */
    public function show(Request $request, $order_number, $template)
    {
        return $this->getPdfResponse(OrderService::getOrderPdf($order_number, $template));
    }
}
