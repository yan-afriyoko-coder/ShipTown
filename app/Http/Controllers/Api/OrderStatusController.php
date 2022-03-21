<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStatusIndexRequest;
use App\Http\Resources\OrderStatusResource;
use App\Models\OrderStatus;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param OrderStatusIndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(OrderStatusIndexRequest $request) : AnonymousResourceCollection
    {
        $query = OrderStatus::getSpatieQueryBuilder();

        return OrderStatusResource::collection($this->getPaginatedResult($query));
    }
}
