<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderShipmentStoreRequest;
use App\Http\Resources\OrderShipmentResource;
use App\Models\OrderShipment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
        $shipment = new OrderShipment($request->validated());
        $shipment->user()->associate($request->user());
        $shipment->save();

        return new OrderShipmentResource($shipment);
    }
}
