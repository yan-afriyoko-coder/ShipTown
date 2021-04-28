<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\UpdateRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

/**
 * Class OrdersController
 * @package App\Http\Controllers\Api
 */
class OrderController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = Order::getSpatieQueryBuilder();

        return OrderResource::collection($this->getPaginatedResult($query));
    }

    /**
     * @param StoreOrderRequest $request
     * @return JsonResponse
     */
    public function store(StoreOrderRequest $request)
    {
        $order = Order::query()->updateOrCreate(
            ['order_number' => $request->order_number],
            $request->validated()
        );

        collect($request['products'])->each(function ($orderProductData) use ($order) {
            $product = Product::findBySKU($orderProductData['sku']);
            OrderProduct::create([
                'order_id' => $order->getKey(),
                'product_id' => $product ? $product->getKey() : null,
                'sku_ordered' => $orderProductData['sku'],
                'name_ordered' => $orderProductData['name'],
                'quantity_ordered' => $orderProductData['quantity'],
                'price' => $orderProductData['price'],
            ]);
        });

        return response()->json($order);
    }

    /**
     * @param UpdateRequest $request
     * @param Order $order
     * @return JsonResource
     */
    public function update(UpdateRequest $request, Order $order)
    {
        $updates = $request->validated();

        if ($request->has('packer_user_id')) {
            Order::query()
                ->whereNull('packed_at')
                ->where(['packer_user_id' => $request->get('packer_user_id')])
                ->whereKeyNot($order->getKey())
                ->update(['packer_user_id' => null]);
        }

        if ($request->has('is_packed')) {
            if ($order->is_packed) {
                $this->respondNotAllowed405('Order already packed!');
            }

//            if ($order->packer_user_id != $request->user()->getKey()) {
//                $this->respondNotAllowed405('Order is being packed by another user');
//            }

            $updates = Arr::add($updates, 'packer_user_id', $request->user()->getKey());
        }

        $order->update($updates);

        return new JsonResource($order);
    }

    /**
     * @param Request $request
     * @param Order $order
     * @return JsonResource
     */
    public function show(Request $request, Order $order)
    {
        return new JsonResource($order);
    }
}
