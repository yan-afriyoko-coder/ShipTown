<?php

namespace App\Modules\DpdUk\src\Services;

use App\Abstracts\ShippingServiceAbstract;
use App\Models\Order;
use App\Models\ShippingLabel;
use App\Modules\PrintNode\src\PrintNode;
use Exception;
use Illuminate\Support\Collection;

/**
 *
 */
class NextDayShippingService extends ShippingServiceAbstract
{
    /**
     * @throws Exception
     */
    public function ship(int $order_id): Collection
    {
        /** @var Order $order */
        $order = Order::findOrFail($order_id);

        $shipment = (new DpdUkService())->createShippingLabel($order);

        $this->printShipment($shipment);

        return collect()->add($shipment);
    }

    /**
     * @param ShippingLabel $shipment
     */
    private function printShipment(ShippingLabel $shipment): void
    {
        if (isset(auth()->user()->printer_id)) {
            PrintNode::printRaw($shipment->base64_pdf_labels, auth()->user()->printer_id);
        }
    }
}
