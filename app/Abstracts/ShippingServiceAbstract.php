<?php

namespace App\Abstracts;

use App\Exceptions\ShippingServiceException;
use Illuminate\Support\Collection;

abstract class ShippingServiceAbstract
{
    /**
     * @throws ShippingServiceException
     */
    abstract public function ship(int $order_id): Collection;
}
