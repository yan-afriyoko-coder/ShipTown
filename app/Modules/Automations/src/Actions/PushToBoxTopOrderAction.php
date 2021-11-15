<?php

namespace App\Modules\Automations\src\Actions;

use App\Models\OrderShipment;
use App\Modules\Automations\src\BaseOrderAction;
use App\Modules\BoxTop\src\Services\BoxTopService;
use Exception;

class PushToBoxTopOrderAction extends BaseOrderAction
{
    public function handle($options)
    {
        parent::handle($options);

        try {
            $shipment = OrderShipment::whereOrderId($this->order->getKey())->first();

            if ($shipment) {
                // order already shipped
                return;
            }

            /** @var OrderShipment $shipment */
            $shipment = OrderShipment::create([
                'order_id' => $this->order->getKey(),
                'shipping_number' => '',
            ]);

            $response = BoxTopService::postOrder($this->order);

            if (201 === $response->http_response->getStatusCode()) {
                $shipment->carrier = 'BoxTop';
                $shipment->shipping_number = $response->toArray()['WarehouseJobNumber'];
                $shipment->save();

                $this->order->log('BoxTop pick created - '. $response->content);

                $this->order->status_code = 'complete_twickenham';
                $this->order->save();
                return;
            }

            $shipment->delete();
            $this->order->log('BoxTop pick FAILED - '. $response->content);
        } catch (Exception $exception) {
            $shipment->delete();
            $this->order->log('BoxTop pick FAILED - '. $exception->getMessage());
        }
    }
}
