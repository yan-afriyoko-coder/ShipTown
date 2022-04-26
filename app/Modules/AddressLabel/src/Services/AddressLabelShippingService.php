<?php

namespace App\Modules\AddressLabel\src\Services;

use App\Abstracts\ShippingServiceAbstract;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressLabelShippingService extends ShippingServiceAbstract
{
    public function ship(int $order_id): AnonymousResourceCollection
    {
        // TODO: Implement ship() method.
        return JsonResource::collection([]);
    }
}
