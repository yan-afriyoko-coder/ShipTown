<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderProduct;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
                'order_id'
            ])
            ->allowedIncludes([
                'product',
                'product.aliases',
            ]);

        $per_page = $request->get('per_page', 10);

        return $query->paginate($per_page)->appends($request->query());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->respondNotAllowed405();
    }

    /**
     * Display the specified resource.
     *
     * @param  OrderProduct  $orderProduct
     * @return Response
     */
    public function show(OrderProduct $orderProduct)
    {
        $this->respondNotAllowed405();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  OrderProduct  $orderProduct
     * @return Response
     */
    public function update(Request $request, OrderProduct $orderProduct)
    {
        $this->respondNotAllowed405();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  OrderProduct  $orderProduct
     * @return Response
     */
    public function destroy(OrderProduct $orderProduct)
    {
        $this->respondNotAllowed405();
    }
}
