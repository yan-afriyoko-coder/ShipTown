<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderPicklistResource;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class OrderPicklistController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(OrderProduct::class);

        return OrderPicklistResource::collection($this->getPerPageAndPaginate($request, $query, 10));
    }
}
