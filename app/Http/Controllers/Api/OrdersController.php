<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\UpdateRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrdersController extends Controller
{

    public function index(Request $request)
    {
        $query = QueryBuilder::for(Order::class)
            ->allowedFilters([
                'status_code',
                AllowedFilter::partial('status', 'status_code'),
                AllowedFilter::exact('order_number'),
                AllowedFilter::scope('is_picked'),
                AllowedFilter::scope('is_packed'),
            ])
            ->allowedIncludes([
                'shipping_address',
                'order_products',
                'order_products.product',
            ]);

        if ($request->has('q') && $request->get('q')) {
            $query->where('order_number', 'like', '%' . $request->get('q') . '%');
        }

        $per_page = $request->get('per_page', 10);

        return $query->paginate($per_page);
    }

    public function store(StoreOrderRequest $request)
    {
        $order = Order::query()->updateOrCreate(
            ['order_number' => $request->order_number],
            $request->all()
        );

        return response()->json($order, 200);
    }

    /**
     * @param UpdateRequest $request
     * @param Order $order
     * @return JsonResource
     */
    public function update(UpdateRequest $request, Order $order)
    {
        $order->update($request->validated());

        return new JsonResource($order);
    }

    public function show(Request $request, Order $order)
    {
        return new JsonResource($order);
    }

    public function destroy($order_number)
    {
        try {
            $order = Order::query()->where('order_number', $order_number)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound();
        }

        $order->delete();

        return $this->respondOK200();
    }
}
