<?php

namespace App\Modules\AddressLabel\src\Services;

use App\Abstracts\ShippingServiceAbstract;
use App\Models\Order;
use App\Modules\PrintNode\src\Models\PrintJob;
use App\Modules\PrintNode\src\PrintNode;
use App\Modules\PrintNode\src\Resources\PrintJobResource;
use App\Services\OrderService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class AddressLabelShippingService extends ShippingServiceAbstract
{
    public function ship(int $order_id): AnonymousResourceCollection
    {
        /** @var Order $order */
        $order = Order::findOrFail($order_id);
        $pdfString = OrderService::getOrderPdf($order->order_number, 'address_label');

        $printJob = new PrintJob();
        $printJob->printer_id = Auth::user()->printer_id;
        $printJob->title = 'address_label_'.$order->order_number.'_by_'.Auth::id();
        $printJob->pdf = base64_encode($pdfString);

        PrintNode::print($printJob);

        return PrintJobResource::collection([$printJob]);
    }
}
