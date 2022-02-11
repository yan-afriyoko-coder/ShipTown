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
        $orderStatuses = OrderStatus::all();

        return OrderStatusResource::collection($orderStatuses);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return OrderStatusResource
     */
    public function store(StoreRequest $request)
    {
        $orderStatus = OrderStatus::where('code', $request->code)->onlyTrashed()->first();
        if ($orderStatus) {
            $orderStatus->restore();
            $orderStatus->update($request->validated());
        } else {
            $this->validate($request, [
                'name' => 'unique:orders_statuses,name',
                'code' => 'unique:orders_statuses,code',
            ]);

            $orderStatus = new OrderStatus;
            $orderStatus->fill($request->validated());
            $orderStatus->save();
        }

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
        $orderStatus->fill($request->validated());
        $orderStatus->save();

        return OrderStatusResource::make($orderStatus);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  OrderStatus $orderStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderStatus $orderStatus)
    {
        if ($orderStatus->order_active || $orderStatus->reserves_stock || $orderStatus->sync_ecommerce) {
            abort(401, "This order statuses cannot archived");
        }

        $orderStatus->delete();

        return true;
    }
}
