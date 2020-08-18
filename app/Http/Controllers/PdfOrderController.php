<?php

namespace App\Http\Controllers;

use App\Jobs\Api2cart\ImportShippingAddressJob;
use App\Models\Order;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class AddressLabel
 * @package App\Http\Controllers\Orders
 */
class PdfOrderController extends PdfBaseController
{
    /**
     * @param Request $request
     * @param string $order_number
     * @param string $template
     * @return ResponseFactory|Response
     */
    public function show(Request $request, $order_number, $template)
    {
        $order = Order::query()
            ->where(['order_number' => $order_number])
            ->with('shippingAddress')
            ->firstOrFail();

        if(!$order->shipping_address_id) {
            ImportShippingAddressJob::dispatchNow($order->id);
            $order = $order->refresh();
        }

        $view       = 'pdf/orders/'. $template;
        $data       = $order->toArray();

        return $this->getPdfResponse($view, $data);
    }

}
