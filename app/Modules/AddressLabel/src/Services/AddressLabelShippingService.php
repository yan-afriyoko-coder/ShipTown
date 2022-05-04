<?php

namespace App\Modules\AddressLabel\src\Services;

use App\Abstracts\ShippingServiceAbstract;
use App\Models\Order;
use App\Models\ShippingLabel;
use App\Modules\PrintNode\src\PrintNode;
use App\Services\OrderService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressLabelShippingService extends ShippingServiceAbstract
{
    public function ship(int $order_id): AnonymousResourceCollection
    {
        /** @var Order $order */
        $order = Order::findOrFail($order_id);
        $pdfString = OrderService::getOrderPdf($order->order_number, 'address_label');

        $shippingLabel = new ShippingLabel();
        $shippingLabel->order()->associate($order);
        $shippingLabel->carrier = '';
        $shippingLabel->service = 'address_label';
        $shippingLabel->shipping_number = $order->order_number;
        $shippingLabel->base64_pdf_labels = base64_encode($pdfString);
        $shippingLabel->save();

        $this->printShippingLabel($shippingLabel);

        return JsonResource::collection([$shippingLabel]);
    }

    /**
     * @param ShippingLabel $shippingLabel
     */
    private function printShippingLabel(ShippingLabel $shippingLabel): void
    {
        if (isset(auth()->user()->printer_id)) {
            PrintNode::printRaw($shippingLabel->base64_pdf_labels, auth()->user()->printer_id);
        }
    }
}
