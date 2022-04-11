<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ShippingCourierResource;
use App\Models\ShippingCourier;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShippingCourierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $query = ShippingCourier::getSpatieQueryBuilder();

        return ShippingCourierResource::collection($this->getPaginatedResult($query));
    }
}
