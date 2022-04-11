<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShippingLabelRequest;

class ShippingLabelController extends Controller
{
    public function store(StoreShippingLabelRequest $request)
    {
        dd($request->validated());
        $this->respondBadRequest('Haha all good!');
    }
}
