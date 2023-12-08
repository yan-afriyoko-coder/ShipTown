<?php

namespace App\Modules\ScurriAnpost\src\Services;

use App\Abstracts\ShippingServiceAbstract;
use App\Models\Order;
use App\Models\ShippingLabel;
use App\Modules\PrintNode\src\PrintNode;
use App\Modules\PrintNode\src\PrintNodeServiceProvider;
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
     * @throws Exception
     */
    private function printShippingLabel(ShippingLabel $orderShipment): void
    {
        if (PrintNodeServiceProvider::isDisabled()) {
            throw new Exception('PrintNode module is disabled, shipping label generated but could not print');
        }

        /** @var User $user */
        $user = auth()->user();

        if (data_get($user, 'printer_id', 0) === 0) {
            throw new Exception('User does not have printer selected, shipping label generated but could not print');
        }

        $orderShipment->user()->associate(auth()->user());

        PrintNode::printBase64Pdf($orderShipment->base64_pdf_labels, $user->printer_id);
    }
}
