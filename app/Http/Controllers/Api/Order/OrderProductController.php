<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderProducts\UpdateRequest;
use App\Http\Resources\OrderProductResource;
use App\Models\OrderProduct;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrderProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = QueryBuilder::for(OrderProduct::class)
            ->allowedFilters([
                AllowedFilter::exact('product_id'),
                AllowedFilter::exact('order_id'),
                AllowedFilter::scope('inventory_source_location_id', 'addInventorySource')->default(100),
            ])
            ->allowedIncludes([
                'order',
                'product',
                'product.aliases',
            ])
            ->allowedSorts([
                'inventory_source_shelf_location',
                'sku_ordered',
                'id',
            ]);

        return OrderProductResource::collection($this->getPerPageAndPaginate($request, $query));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param $id
     * @return OrderProductResource
     */
    public function update(UpdateRequest $request, $id)
    {
        $orderProduct = OrderProduct::findOrFail($id);

        $orderProduct->update($request->validated());

        return new OrderProductResource($orderProduct);
    }
}
