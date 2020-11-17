<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderCommentStoreRequest;
use App\Http\Resources\OrderCommentResource;
use App\Models\OrderComment;

class OrderCommentController extends Controller
{
    public function store(OrderCommentStoreRequest $request)
    {
        $shipment = new OrderComment($request->validated());
        $shipment->user()->associate($request->user());
        $shipment->save();

        return OrderCommentResource::collection(collect([$shipment]));
    }
}
