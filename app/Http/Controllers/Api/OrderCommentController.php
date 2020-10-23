<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\OrderCommentStoreRequest;
use App\Http\Resources\OrderCommentResource;
use App\Models\OrderComment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderCommentController extends Controller
{
    public function store(OrderCommentStoreRequest $request)
    {
        $shipment = new OrderComment($request->validated());
        $shipment->user()->associate($request->user());
        $shipment->save();

        return new OrderCommentResource($shipment);
    }
}
