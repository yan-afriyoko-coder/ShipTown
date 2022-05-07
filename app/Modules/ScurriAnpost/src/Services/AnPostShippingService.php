<?php

namespace App\Modules\ScurriAnpost\src\Services;

use App\Abstracts\ShippingServiceAbstract;
use App\Models\Order;
use App\Models\ShippingLabel;
use App\Modules\PrintNode\src\PrintNode;
use App\Modules\ScurriAnpost\src\Scurri;
use App\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class AnPostShippingService extends ShippingServiceAbstract
{
    /**
     * @throws Exception
     */
    public function ship(int $order_id): Collection
    {
        /** @var Order $order */
        $order = Order::findOrFail($order_id);

        $orderShipment = Scurri::createShippingLabel($order);
        $orderShipment->user_id = Auth::id();
        $orderShipment->save();

        $this->printShippingLabel($orderShipment);

        return collect()->add($orderShipment);
    }

    /**
     * @param ShippingLabel $orderShipment
     */
    private function printShippingLabel(ShippingLabel $orderShipment): void
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user) {
            $orderShipment->user()->associate(auth()->user());

            if ($user->printer_id) {
                PrintNode::printBase64Pdf($orderShipment->base64_pdf_labels, $user->printer_id);
            }
        }
    }
}
