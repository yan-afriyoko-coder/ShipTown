<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShipmentStoreRequestNew;
use App\Http\Resources\Api\ShipmentResource;
use App\Models\OrderShipment;
use App\Modules\DpdUk\src\Jobs\GenerateLabelDocumentJob;
use Exception;

class ShipmentControllerNew extends Controller
{
    /**ø
     * @param ShipmentStoreRequestNew $request
     * @return mixed
     */
    public function store(ShipmentStoreRequestNew $request)
    {
        $shipment = new OrderShipment($request->validated());
        $shipment->user_id = $request->user()->getKey();

        try {
            GenerateLabelDocumentJob::dispatchNow($shipment);
        } catch (Exception $exception) {
            $this->respondBadRequest($exception->getMessage());
        }

        return ShipmentResource::make($shipment);
    }
}
