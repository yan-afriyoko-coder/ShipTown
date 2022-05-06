<?php


namespace App\Http\Controllers\Api\Modules\Scurri;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderShipmentResource;
use App\Models\Order;
use App\Modules\PrintNode\src\PrintNode;
use App\Modules\ScurriAnpost\src\Scurri;
use App\User;
use Exception;
use Illuminate\Http\Request;

class ShipmentsController extends Controller
{
    /**
     * @throws Exception
     */
    public function store(Request $request, string $order_number): OrderShipmentResource
    {
        $order = Order::whereOrderNumber($order_number)->firstOrFail();

        $orderShipment = Scurri::createShippingLabel($order);
        $orderShipment->user()->associate($request->user());
        $orderShipment->save();

        /** @var User $user */
        $user = $request->user();

        if ($user->printer_id) {
            PrintNode::printBase64Pdf($orderShipment->base64_pdf_labels, $user->printer_id);
        }

        return OrderShipmentResource::make($orderShipment);
    }
}
