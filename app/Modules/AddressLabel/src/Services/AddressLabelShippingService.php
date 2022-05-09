<?php

namespace App\Modules\AddressLabel\src\Services;

use App\Abstracts\ShippingServiceAbstract;
use App\Models\Order;
use App\Models\ShippingLabel;
use App\Modules\PrintNode\src\PrintNode;
use App\Services\OrderService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 *
 */
class AddressLabelShippingService extends ShippingServiceAbstract
{
    /**
     * @param int $order_id
     * @return Collection
     */
    public function ship(int $order_id): Collection
    {
        /** @var Order $order */
        $order = Order::findOrFail($order_id);

        $shippingLabel = $this->createShippingLabel($order);

        $this->print($shippingLabel);

        return collect([$shippingLabel]);
    }

    /**
     * @param ShippingLabel $shippingLabel
     */
    private function print(ShippingLabel $shippingLabel): void
    {
        if (isset(auth()->user()->printer_id)) {
            PrintNode::printBase64Pdf($shippingLabel->base64_pdf_labels, auth()->user()->printer_id);
        }
    }

    /**
     * @param Order $order
     * @return ShippingLabel
     */
    private function createShippingLabel(Order $order): ShippingLabel
    {
        $pdfString = OrderService::getOrderPdf($order->order_number, 'address_label');

        $shippingLabel = new ShippingLabel();
        $shippingLabel->order()->associate($order);
        $shippingLabel->user_id = auth()->id();
        $shippingLabel->carrier = '';
        $shippingLabel->service = 'address_label';
        $shippingLabel->shipping_number = $order->order_number;
        $shippingLabel->base64_pdf_labels = base64_encode($pdfString);
        $shippingLabel->save();
        return $shippingLabel;
    }
}
