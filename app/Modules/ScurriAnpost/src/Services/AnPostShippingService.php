<?php

namespace App\Modules\ScurriAnpost\src\Services;

use App\Abstracts\ShippingServiceAbstract;
use App\Http\Resources\OrderShipmentResource;
use App\Models\Order;
use App\Modules\PrintNode\src\PrintNode;
use App\Modules\ScurriAnpost\src\Scurri;
use App\User;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AnPostShippingService extends ShippingServiceAbstract
{
    /**
     * @throws Exception
     */
    public function ship(int $order_id): AnonymousResourceCollection
    {
        /** @var Order $order */
        $order = Order::findOrFail($order_id);

        $orderShipment = Scurri::createOrderShipment($order);
        $orderShipment->save();

        /** @var User $user */
        $user = auth()->user();

        if ($user) {
            $orderShipment->user()->associate(auth()->user());

            if ($user->printer_id) {
                PrintNode::printBase64Pdf($orderShipment->base64_pdf_labels, $user->printer_id);
            }
        }

        return OrderShipmentResource::collection(collect([$orderShipment]));
    }
}
