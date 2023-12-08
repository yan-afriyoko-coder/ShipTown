<?php

namespace App\Modules\DpdUk\src\Services;

use App\Abstracts\ShippingServiceAbstract;
use App\Models\Order;
use Exception;
use Illuminate\Support\Collection;

/**
 *
 */
class NextDayShippingService extends ShippingServiceAbstract
{
    /**
     * @throws Exception
     */
    public function ship(int $order_id): Collection
    {
        /** @var Order $order */
        $order = Order::findOrFail($order_id);

        $shipment = (new DpdUkService())->createShippingLabel($order);

        return collect()->add($shipment);
    }
}
