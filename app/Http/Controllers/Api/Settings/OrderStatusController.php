<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStatus\StoreRequest;
use App\Http\Requests\OrderStatus\UpdateRequest;
use App\Http\Resources\OrderStatusResource;
use App\Models\OrderStatus;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class OrderStatusController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index() : AnonymousResourceCollection
    {
        $query = OrderStatus::getSpatieQueryBuilder();

        return OrderStatusResource::collection($this->getPaginatedResult($query));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return OrderStatusResource
     * @throws ValidationException
     */
    public function store(StoreRequest $request): OrderStatusResource
    {
        $orderStatus = OrderStatus::where(['code' => $request->validated()['code']])->onlyTrashed()->first();

        if ($orderStatus) {
            $orderStatus->restore();
            return OrderStatusResource::make($orderStatus);
        }

        $this->validate($request, ['code' => 'unique:orders_statuses,code']);

        $orderStatus = OrderStatus::create($request->validated());

        return OrderStatusResource::make($orderStatus);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param OrderStatus $orderStatus
     * @return OrderStatusResource
     */
    public function update(UpdateRequest $request, OrderStatus $orderStatus): OrderStatusResource
    {
        $orderStatus->fill($request->validated());
        $orderStatus->save();

        return OrderStatusResource::make($orderStatus);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param OrderStatus $orderStatus
     * @return void
     * @throws Exception
     */
    public function destroy(OrderStatus $orderStatus)
    {
        if ($orderStatus->order_active || $orderStatus->reserves_stock || $orderStatus->sync_ecommerce) {
            abort(401, "This order statuses cannot archived");
        }

        $orderStatus->delete();
    }
}
