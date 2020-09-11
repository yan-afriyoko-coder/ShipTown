<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderShipmentStoreRequest;
use App\Http\Resources\OrderShipmentResource;
use App\Models\OrderShipment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class OrderShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $pick = QueryBuilder::for(OrderShipment::class)
            ->allowedFilters([
            ])
            ->allowedIncludes([
            ])
            ->allowedSorts([
            ]);

        $per_page = $request->get('per_page', 10);

        return $pick->paginate($per_page)->appends($request->query());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OrderShipmentStoreRequest $request
     * @return OrderShipmentResource
     */
    public function store(OrderShipmentStoreRequest $request)
    {
        $shipment = new OrderShipment();

        $shipment->fill($request->validated());

        $shipment->save();

        return new OrderShipmentResource($shipment);
    }

    /**
     * Display the specified resource.
     *
     * @param OrderShipment $orderShipment
     * @return JsonResponse
     */
    public function show(OrderShipment $orderShipment)
    {
        return $this->getResponse(405, 'Method not allowed');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param OrderShipment $orderShipment
     * @return JsonResponse
     */
    public function update(Request $request, OrderShipment $orderShipment)
    {
        return $this->getResponse(405, 'Method not allowed');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param OrderShipment $orderShipment
     * @return JsonResponse
     */
    public function destroy(OrderShipment $orderShipment)
    {
        return $this->getResponse(405, 'Method not allowed');
    }
}
