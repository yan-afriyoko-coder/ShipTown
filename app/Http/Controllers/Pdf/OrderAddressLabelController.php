<?php

namespace App\Http\Controllers\Pdf;

use App\Jobs\Api2cart\ImportShippingAddressJob;
use App\Models\Order;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Milon\Barcode\DNS1D;

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

        if(!$order->shipping_address_id) {
            ImportShippingAddressJob::dispatchNow($order->id);
            $order = $order->refresh();
        }

        $filename   = $order_number . '.pdf"';
        $view       = 'pdf/orders/address_label';
        $data       = $order->toArray();

        return $this->getPdf($view, $data, $filename);
    }

}
