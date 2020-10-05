<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderProducts\UpdateRequest;
use App\Http\Resources\OrderProductResource;
use App\Models\OrderProduct;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrderProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return LengthAwarePaginator
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

        $per_page = $request->get('per_page', 10);

        return $query->paginate($per_page)->appends($request->query());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        return $this->respondNotAllowed405();
    }

    /**
     * Display the specified resource.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function show(OrderProduct $orderProduct)
    {
        return $this->respondNotAllowed405();
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  OrderProduct  $orderProduct
     * @return void
     */
    public function destroy(OrderProduct $orderProduct)
    {
        return $this->respondNotAllowed405();
    }
}
