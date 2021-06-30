<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStatus\StoreRequest;
use App\Http\Requests\OrderStatus\UpdateRequest;
use App\Http\Resources\OrderStatusResource;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() : AnonymousResourceCollection
    {
        $modules = OrderStatus::all();

        return OrderStatusResource::collection($modules);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return OrderStatusResource
     */
    public function store(StoreRequest $request)
    {
        $orderStatus = new OrderStatus;
        $orderStatus->name = $request->name;
        $orderStatus->code = $request->code;
        $orderStatus->reserves_stock = $request->reserves_stock;
        $orderStatus->order_active = $request->order_active;
        $orderStatus->save();

        return OrderStatusResource::make($orderStatus);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  OrderStatus $orderStatus
     * @return OrderStatusResource
     */
    public function update(UpdateRequest $request, OrderStatus $orderStatus)
    {
        $orderStatus->reserves_stock = $request->reserves_stock;
        $orderStatus->order_active = $request->order_active;
        $orderStatus->save();

        return OrderStatusResource::make($orderStatus);
    }
}
