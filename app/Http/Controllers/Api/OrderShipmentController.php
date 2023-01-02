<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderShipmentStoreRequest;
use App\Http\Resources\OrderShipmentResource;
use App\Models\OrderShipment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class OrderShipmentController.
 *
 * @group Order
 */
class OrderShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        $query = OrderShipment::getSpatieQueryBuilder();

        return $this->getPaginatedResult($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OrderShipmentStoreRequest $request
     *
     * @return OrderShipmentResource
     */
    public function store(OrderShipmentStoreRequest $request): OrderShipmentResource
    {
        $shipment = new OrderShipment($request->validated());
        $shipment->user()->associate($request->user());
        $shipment->save();

        return new OrderShipmentResource($shipment);
    }
}
