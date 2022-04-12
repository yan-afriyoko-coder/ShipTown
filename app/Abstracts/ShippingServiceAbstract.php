<?php

namespace App\Abstracts;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

abstract class ShippingServiceAbstract
{
    abstract public function ship(int $order_id): AnonymousResourceCollection;
}
