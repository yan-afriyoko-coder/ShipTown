<?php

namespace App\Http\Controllers\Pdf;

use App\Models\Order;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class AddressLabel
 * @package App\Http\Controllers\Orders
 */
class OrderAddressLabelController extends PdfBaseController
{
    /**
     * @param Request $request
     * @param $order_number
     * @return ResponseFactory|Response
     */
    public function index(Request $request, $order_number)
    {
        $order = Order::query()
            ->where(['order_number' => $order_number])
            ->with('shippingAddress')
            ->firstOrFail();

        $filename   = $order_number . '.pdf"';
        $view       = 'pdf/orders/address_label';
        $data       = $order->toArray();

        return $this->getPdf($view, $data, $filename);
    }

}
