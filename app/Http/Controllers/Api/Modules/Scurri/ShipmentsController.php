<?php


namespace App\Http\Controllers\Api\Modules\Scurri;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderShipmentResource;
use App\Models\Order;
use App\Modules\ScurriAnpost\src\Scurri;
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

        $orderShipment = Scurri::createOrderShipment($order);
        $orderShipment->user()->associate($request->user());
        $orderShipment->save();

        return OrderShipmentResource::make($orderShipment);
    }
}
