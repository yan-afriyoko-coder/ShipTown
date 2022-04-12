<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShippingLabelRequest;
use App\Http\Resources\ShippingLabelResource;
use App\Models\OrderShipment;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 *
 */
class ShippingLabelController extends Controller
{
    /**
     * @param StoreShippingLabelRequest $request
     * @return AnonymousResourceCollection
     */
    public function store(StoreShippingLabelRequest $request): AnonymousResourceCollection
    {
        $query = OrderShipment::getSpatieQueryBuilder();

        return ShippingLabelResource::collection($this->getPaginatedResult($query));
    }
}
