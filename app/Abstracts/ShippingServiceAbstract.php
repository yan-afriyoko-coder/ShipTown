<?php

namespace App\Abstracts;

use Illuminate\Support\Collection;

abstract class ShippingServiceAbstract
{
    abstract public function ship(int $order_id): Collection;
}
