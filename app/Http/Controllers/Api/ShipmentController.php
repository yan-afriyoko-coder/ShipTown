<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Shipment\StoreRequest;
use App\Http\Requests\OrderShipmentStoreRequest;
use App\Http\Resources\Api\ShipmentResource;
use App\Http\Resources\OrderShipmentResource;
use App\Models\OrderShipment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ShipmentController extends Controller
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
     * @param StoreRequest $request
     *
     * @return ShipmentResource
     */
    public function store(StoreRequest $request): ShipmentResource
    {
        $shipment = new OrderShipment($request->validated());
        $shipment->user()->associate($request->user());
        $shipment->save();

        return new ShipmentResource($shipment);
    }
}
